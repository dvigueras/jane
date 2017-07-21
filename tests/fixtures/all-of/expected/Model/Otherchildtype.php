<?php

namespace Joli\Jane\Tests\Expected\Model;

class Otherchildtype
{
    /**
     * @var string
     */
    protected $inheritedProperty;
    /**
     * @var string
     */
    protected $childProperty;

    /**
     * @return string
     */
    public function getInheritedProperty()
    {
        return $this->inheritedProperty;
    }

    /**
     * @param string $inheritedProperty
     *
     * @return self
     */
    public function setInheritedProperty($inheritedProperty = null)
    {
        $this->inheritedProperty = $inheritedProperty;

        return $this;
    }

    /**
     * @return string
     */
    public function getChildProperty()
    {
        return $this->childProperty;
    }

    /**
     * @param string $childProperty
     *
     * @return self
     */
    public function setChildProperty($childProperty = null)
    {
        $this->childProperty = $childProperty;

        return $this;
    }
}