<?php

namespace Joli\Jane\Guesser\JsonSchema;

use Joli\Jane\Generator\Naming;
use Joli\Jane\Guesser\ChainGuesserAwareInterface;
use Joli\Jane\Guesser\ChainGuesserAwareTrait;
use Joli\Jane\Guesser\ClassGuesserInterface;
use Joli\Jane\Guesser\Guess\ClassGuess;
use Joli\Jane\Guesser\Guess\Type;
use Joli\Jane\Guesser\GuesserInterface;
use Joli\Jane\Guesser\GuesserResolverTrait;
use Joli\Jane\Guesser\PropertiesGuesserInterface;
use Joli\Jane\Guesser\TypeGuesserInterface;
use Joli\Jane\Model\JsonSchema;
use Joli\Jane\Registry;
use Joli\Jane\Runtime\Reference;
use Symfony\Component\Serializer\SerializerInterface;

class AllOfGuesser implements GuesserInterface, TypeGuesserInterface, ChainGuesserAwareInterface, PropertiesGuesserInterface, ClassGuesserInterface
{
    use ChainGuesserAwareTrait;
    use GuesserResolverTrait;

    private $naming;

    public function __construct(SerializerInterface $serializer, Naming $naming)
    {
        $this->serializer = $serializer;
        $this->naming = $naming;
    }

    /**
     * {@inheritdoc}
     */
    public function guessClass($object, $name, $reference, Registry $registry)
    {
        $hasSubObject = false;

        foreach ($object->getAllOf() as $allOf) {
            if ($this->resolve($allOf, $this->getSchemaClass())->getType() === 'object') {
                $hasSubObject = true;
                break;
            }
        }

        if ($hasSubObject) {
            if (!$registry->hasClass($reference)) {
                $registry->getSchema($reference)->addClass($reference, new ClassGuess($object, $reference, $this->naming->getClassName($name)));
            }

            foreach ($object->getAllOf() as $allOfIndex => $allOf) {
                if (is_a($allOf, $this->getSchemaClass())) {
                    if ($allOf->getProperties()) {
                        foreach ($allOf->getProperties() as $key => $property) {
                            $this->chainGuesser->guessClass($property, $name . $key, $reference . '/allOf/' . $allOfIndex . '/properties/' . $key, $registry);
                        }
                    }
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function guessType($object, $name, $reference, Registry $registry)
    {
        $type = null;
        $allOfType = null;

        foreach ($object->getAllOf() as $allOfIndex => $allOf) {
            $allOfSchema = $allOf;
            $allOfReference = $reference . '/allOf/' . $allOfIndex;

            if ($allOfSchema instanceof Reference) {
                $allOfReference = (string) $allOf->getMergedUri();

                if ((string)$allOf->getMergedUri() === (string)$allOf->getMergedUri()->withFragment('')) {
                    $allOfReference .= '#';
                }

                $allOfSchema = $this->resolve($allOfSchema, $this->getSchemaClass());
            }

            if (null !== $allOfSchema->getType()) {
                if (null !== $type && $allOfType !== $allOfSchema->getType()) {
                    throw new \RuntimeException('an allOf instruction with 2 or more types is strictly impossible, check your schema');
                }

                $allOfType = $allOfSchema->getType();
                $type = $this->chainGuesser->guessType($allOf, $name, $allOfReference, $registry);
            }
        }

        if ($type === null) {
            return new Type($object, 'mixed');
        }

        return $type;
    }

    /**
     * {@inheritdoc}
     */
    public function supportObject($object)
    {
        return ($object instanceof JsonSchema) && is_array($object->getAllOf()) && count($object->getAllOf()) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function guessProperties($object, $name, $reference, Registry $registry)
    {
        $properties = [];
        foreach ($object->getAllOf() as $allOfIndex => $allOfSchema) {
            $allOfReference = $reference . '/allOf/' . $allOfIndex;

            if ($allOfSchema instanceof Reference) {
                $allOfReference = (string) $allOfSchema->getMergedUri();

                if ((string)$allOfSchema->getMergedUri() === (string)$allOfSchema->getMergedUri()->withFragment('')) {
                    $allOfReference .= '#';
                }

                $allOfSchema = $this->resolve($allOfSchema, $this->getSchemaClass());
            }

            $properties = array_merge($properties, $this->chainGuesser->guessProperties($allOfSchema, $name, $allOfReference, $registry));
        }

        return $properties;
    }

    /**
     * @return string
     */
    protected function getSchemaClass()
    {
        return JsonSchema::class;
    }
}
