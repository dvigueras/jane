<?php

/*
 * This file has been auto generated by Jane,
 *
 * Do no edit it directly.
 */

namespace Joli\Jane\Tests\Expected\Model;

class Baz
{
    /**
     * @var string
     */
    protected $foo;
    /**
     * @var Bar
     */
    protected $bar;
    /**
     * @var BazBaz
     */
    protected $baz;

    /**
     * @return string
     */
    public function getFoo()
    {
        return $this->foo;
    }

    /**
     * @param string $foo
     *
     * @return self
     */
    public function setFoo($foo = null)
    {
        $this->foo = $foo;

        return $this;
    }

    /**
     * @return Bar
     */
    public function getBar()
    {
        return $this->bar;
    }

    /**
     * @param Bar $bar
     *
     * @return self
     */
    public function setBar(Bar $bar = null)
    {
        $this->bar = $bar;

        return $this;
    }

    /**
     * @return BazBaz
     */
    public function getBaz()
    {
        return $this->baz;
    }

    /**
     * @param BazBaz $baz
     *
     * @return self
     */
    public function setBaz(BazBaz $baz = null)
    {
        $this->baz = $baz;

        return $this;
    }
}
