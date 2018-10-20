<?php

namespace Illuminate\Foundation\Testing;

use Illuminate\Support\Collection;
use Illuminate\Contracts\Mail\Mailable;
use PHPUnit\Framework\Assert as PHPUnit;

class TestMailable
{
    /**
     * @var Mailable
     */
    private $mailables;
    /**
     * @var int
     */
    private $times;

    /**
     * Create a new test mailable instance.
     *
     * @param  \Illuminate\Support\Collection $mailables
     * @param int $times
     * @return void
     */
    public function __construct(Collection $mailables, $times = 1)
    {
        $this->mailables = $mailables;
        $this->times = $times;
    }

    public function once()
    {
        return $this->times(1);
    }

    public function twice()
    {
        return $this->times(2);
    }

    /**
     * Assert the minimum number of mailables that has to match all the assertions.
     *
     * @param  integer  $number
     * @return $this
     */
    public function times($number)
    {
        $this->times = $number;

        $this->assertCallback(function () {
             return count($this->mailables) >= $this->times;
        });

        return $this;
    }

    /**
     * Assert if the given recipient is set on the mailable.
     *
     * @param  object|array|string  $address
     * @param  string|null  $name
     * @return $this
     */
    public function to($address, $name = null)
    {
        return $this->assertCallback(function ($mailable) use ($address, $name) {
            return $mailable->hasTo($address, $name);
        }, "to {$address} {$name}"); //TODO: might not be printed corectly if address is object or array
    }

    /**
     * Assert if the given recipient is set on the mailable.
     *
     * @param  object|array|string  $address
     * @param  string|null  $name
     * @return $this
     */
    public function hasCc($address, $name = null)
    {
        return $this->assertCallback(function ($mailable) use ($address, $name) {
            return $mailable->hasCc($address, $name);
        }, "with CC {$address} {$name}"); //TODO: might not be printed corectly if address is object or array
    }

    /**
     * Assert if the given property is set on the mailables
     *
     * @param  string  $property
     * @return \Illuminate\Foundation\Testing\TestData
     */
    public function has($property)
    {
        $result = $this->assertCallback(function ($mailable) use ($property) {
            return isset($mailable->$property);
        }, "with the property {$property}");

        return new TestMultipleData(
            $result->mailables->map(function ($mailable) use ($property) {
                return TestData::make($mailable->$property, $mailable);
            }),
            function ($mailables) {
                return new static($mailables, $this->times);
            },
            $this->times
        );
    }

    /**
     * Assert the mailables based on a truth-test callback.
     *
     * @param  callable  $callback
     * @param  string  $message
     * @return \Illuminate\Foundation\Testing\TestMailable
     */
    protected function assertCallback($callback, $message = '')
    {
        $mailables = $this->mailables->filter($callback);

        if ($mailables->count() < $this->times) {
            PHPUnit::fail($this->failureDescription($message));
        }

        return new static($mailables, $this->times);
    }

    /**
     * Get the description of the failure.
     *
     * @param  string  $message
     * @return string
     */
    protected function failureDescription($message)
    {
        $result = trim("The mailable {$this->name()} was not sent $message");

        if ($this->times > 1) {
            $result .= " {$this->times} times.";
        }

        return $result;
    }

    /**
     * Get the base class name of the mailables under test.
     *
     * @return string
     */
    protected function name()
    {
        return class_basename($this->mailables->first());
    }
}
