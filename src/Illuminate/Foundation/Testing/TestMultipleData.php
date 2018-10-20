<?php

namespace Illuminate\Foundation\Testing;

use PHPUnit\Framework\Assert as PHPUnit;
use PHPUnit\Framework\ExpectationFailedException;

class TestMultipleData
{
    protected $items;
    protected $times;
    protected $parent;

    public function __construct($items, $parent, $times = 1)
    {
        $this->items = collect($items);
        $this->times = $times;
        $this->parent = $parent;
    }

    public function __call($method, $arguments)
    {
        if (method_exists(TestData::class, $method)) {
            return $this->assertEach($method, $arguments);
        }

        if (is_callable($this->parent)) {
            return call_user_func($this->parent, $this->items->map(function ($item) {
                return $item->parent;
            }))->$method(...$arguments);
        }

        return $this->parent->$method(...$arguments);
    }

    protected function assertEach($method, $arguments)
    {
        $result = $this->items->filter(function ($item) use ($method, $arguments, &$message) {
            try {
                $item->$method(...$arguments);

                return true;
            } catch (ExpectationFailedException $e) {
                $message = $e->getMessage();

                return false;
            }
        });

        if ($result->count() < $this->times) {
            PHPUnit::fail($this->failureDescription($message, $result->count()));
        }

        return new static($result, $this->parent, $this->times);
    }

    /**
     * @param  string $message
     * @param  integer $found
     * @return string
     */
    protected function failureDescription($message, $found)
    {
        $message = trim($message, '.');

        if ($this->times > 1) {
            $message .= " {$this->times} times. Found: {$found}.";
        }

        return $message;
    }
}
