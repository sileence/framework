<?php

namespace Illuminate\Foundation\Testing;

use BadMethodCallException;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Assert as PHPUnit;
use Illuminate\Contracts\Pagination\Paginator;
use stdClass;

class TestData
{
    /**
     * The data to assert against.
     *
     * @var mixed
     */
    protected $data;

    /**
     * The parent object to fallback to if a called method is not found.
     *
     * @var null|object
     */
    public $parent;

    /**
     * Create a new test data object.
     *
     * @param  mixed  $data
     * @param  null|object  $parent
     * @return \Illuminate\Foundation\Testing\TestData
     */
    protected function __construct($data, $parent = null)
    {
        $this->data = $data;
        $this->parent = $parent;
    }

    /**
     * Create a new test data object based on the type of the given data.
     *
     * @param  mixed  $data
     * @param  null|object  $parent
     * @return \Illuminate\Foundation\Testing\TestData
     */
    public static function make($data, $parent = null)
    {
        if ($data instanceof Collection || $data instanceof Paginator) {
            return new TestCollectionData($data, $parent);
        }

        return new static($data, $parent);
    }

    /**
     * Asserts that a condition is true.
     *
     * @return $this
     */
    public function isTrue()
    {
        PHPUnit::assertTrue($this->data);

        return $this;
    }

    /**
     * Asserts that a condition is false.
     *
     * @return $this
     */
    public function isFalse()
    {
        PHPUnit::assertFalse($this->data);

        return $this;
    }

    /**
     * Asserts that a variable is null.
     *
     * @return $this
     */
    public function isNull()
    {
        PHPUnit::assertNull($this->data);

        return $this;
    }

    /**
     *  Asserts that a variable is of a given type.
     *
     * @param  string  $className
     * @return $this
     */
    public function isInstanceOf($className)
    {
        PHPUnit::assertInstanceOf($className, $this->data);

        return $this;
    }

    /**
     * Asserts that two variables are equal.
     *
     * @param mixed  $value
     */
    public function equals($value)
    {
        PHPUnit::assertEquals($value, $this->data);
    }

    /**
     * Asserts that two variables have the same type and value.
     * Used on objects, it asserts that two variables reference the same object.
     *
     * @param mixed $value
     * @return $this
     */
    public function is($value)
    {
        PHPUnit::assertSame($value, $this->data);

        return $this;
    }

    /**
     * Asserts the data has the given attribute.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return $this;
     */
    public function with($attribute, $value)
    {
        PHPUnit::assertTrue(
            isset($this->data->$attribute),
            'Failed asserting that object of class "'.get_class($this->data).' has attribute "'.$attribute.'"'
        );

        PHPUnit::assertSame($value, $this->data->$attribute);

        return $this;
    }

    /**
     * Asserts that a haystack contains a needle.
     *
     * @param  mixed  $needle
     * @return $this
     */
    public function contains($needle)
    {
        PHPUnit::assertContains($needle, $this->data);

        return $this;
    }

    /**
     * Asserts that a haystack does not contain a needle.
     *
     * @param  mixed  $needle
     * @return $this
     */
    public function notContains($needle)
    {
        PHPUnit::assertNotContains($needle, $this->data);

        return $this;
    }

    /**
     * Dynamically fallback and call a method in the parent class.
     *
     * @param  string  $method
     * @param  array  $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if ($this->parent) {
            return $this->parent->$method(...$arguments);
        }

        throw new \BadMethodCallException(
            "Call to undefined method Illuminate\Foundation\Testing\TestData::{$method}()"
        );
    }
}
