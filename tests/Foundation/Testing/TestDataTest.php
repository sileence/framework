<?php

namespace Illuminate\Tests\Foundation\Testing;

use PHPUnit\Framework\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\TestData;
use PHPUnit\Framework\ExpectationFailedException;

class TestDataTest extends TestCase
{
    public function testIsTrue()
    {
        TestData::make(true)->isTrue();
    }

    public function testIsTrueFails()
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('Failed asserting that 1 is true.');

        TestData::make(1)->isTrue();
    }

    public function testIsFalse()
    {
        TestData::make(false)->isFalse();
    }

    public function testIsFalseFails()
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('Failed asserting that 0 is false.');

        TestData::make(0)->isFalse();
    }

    public function testIsNull()
    {
        TestData::make(null)->isNull();
    }

    public function testIsNullFails()
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('Failed asserting that false is null.');

        TestData::make(false)->isNull();
    }

    public function testIsInstanceOf()
    {
        TestData::make(new TestUser)->isInstanceOf(TestUser::class);
    }

    public function testIsInstanceOfFails()
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage(
            'Failed asserting that Illuminate\Tests\Foundation\Testing\TestUser Object (...)'
            .' is an instance of class "Illuminate\Tests\Foundation\Testing\TestOrder".'
        );

        TestData::make(new TestUser)->isInstanceOf(TestOrder::class);
    }

    public function testEquals()
    {
        TestData::make(true)->equals(1);
    }

    public function testEqualsFails()
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('Failed asserting that false matches expected 1.');

        TestData::make(false)->equals(1);
    }

    public function testIs()
    {
        $user = new TestUser;

        TestData::make($user)->is($user);
    }

    public function testIsFails()
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('Failed asserting that two variables reference the same object.');

        TestData::make(new TestUser)->is(new TestUser);
    }

    public function testWith()
    {
        $user = new TestUser;

        TestData::make($user)->with('id', 1);
    }

    public function testWithUsingEloquentModel()
    {
        $user = new class(['id' => 5]) extends Model {
            protected $guarded = [];
        };

        TestData::make($user)->with('id', 5);
    }

    public function testWithFailsWhenAttributeIsNotPresent()
    {
        $user = new TestUser;

        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('Failed asserting that object of class "'.get_class($user).' has attribute "user_id"');

        TestData::make($user)->with('user_id', 1);
    }

    public function testWithFailsWhenValuesDontMatch()
    {
        $user = new TestUser;

        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('Failed asserting that 1 is identical to 2.');

        TestData::make($user)->with('id', 2);
    }

    public function testContains()
    {
        TestData::make('Laravel is the best PHP framework')->contains('PHP');
    }

    public function testContainsFails()
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('Failed asserting that \'Laravel is the best PHP framework\' contains "JS".');

        TestData::make('Laravel is the best PHP framework')->contains('JS');
    }

    public function testNotContains()
    {
        TestData::make('Laravel is the best PHP framework')->notContains('JS');
    }

    public function testNotContainsFails()
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('Failed asserting that \'Laravel is not the best PHP framework\' does not contain "PHP".');

        TestData::make('Laravel is the best PHP framework')->notContains('PHP');
    }
}

class TestUser {
    public $id = 1;
}

class TestOrder {

}
