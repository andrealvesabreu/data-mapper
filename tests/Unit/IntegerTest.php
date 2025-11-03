<?php

use Inspire\DataMapper\DataType\{
    Integer,
    Type
};
use Inspire\DataMapper\Exceptions\ValueException;

/**
 * Tests using type constructor.
 */
it('build integer class by its own constructor', function () {
    $integer = new Integer([]);
    expect($integer)->toBeInstanceOf(Integer::class);
})->group('type-constructor');

it('build integer class by its own constructor with default value', function () {
    $integer = new Integer([
        'default' => 15
    ]);
    expect($integer->getValue()->getValue())->toBe('15');
    expect($integer->getValue()->__toString())->toBe('15');
})->group('type-constructor');

it('build integer class by its own constructor with setted value', function () {
    $integer = new Integer([
        'default' => 15,
        'value' => 10
    ]);
    expect($integer->getValue()->getValue())->toBe('10');
    expect($integer->getValue()->__toString())->toBe('10');
})->group('type-constructor');

it('build integer class by its own constructor with range set', function () {
    $integer = new Integer([
        'default' => 15,
        'value' => 10,
        'minimum' => 5,
        'maximum' => 20
    ]);
    expect($integer->getRange()->getMinimum())->toBe(5);
    expect($integer->getRange()->getMaximum())->toBe(20);
})->group('type-constructor');

it('build integer class by its own constructor with size set', function () {
    $integer = new Integer([
        'default' => 15,
        'value' => 10,
        'minLength' => 1,
        'maxLength' => 5
    ]);
    expect($integer->getSize()->getMinimum())->toBe(1);
    expect($integer->getSize()->getMaximum())->toBe(5);
})->group('type-constructor');

it('build integer class by its own constructor with position set', function () {
    $integer = new Integer([
        'default' => 15,
        'value' => 10,
        'start' => 50,
        'end' => 65
    ]);
    expect($integer->getPosition()->getStart())->toBe(50);
    expect($integer->getPosition()->getEnd())->toBe(65);
})->group('type-constructor');

it('build integer class by its own constructor with alignment set', function () {
    $integer = new Integer([
        'default' => 15,
        'value' => 10,
        'align' => 'right'
    ]);
    expect($integer->getAlignment())->toBe(STR_PAD_RIGHT);
})->group('type-constructor');

it('build integer class by its own constructor with value less than range limit', function () {
    $integer = new Integer([
        'default' => 12857,
        'value' => 16,
        'minimum' => 20
    ]);
    expect(function () use ($integer) {
        $integer->validateRange();
    })->toThrow(ValueException::class);
})->group('type-constructor');

it('build integer class by its own constructor with value greater than range limit', function () {
    $integer = new Integer([
        'default' => 624,
        'value' => 5652,
        'maximum' => 30
    ]);
    expect(function () use ($integer) {
        $integer->validateRange();
    })->toThrow(ValueException::class);
})->group('type-constructor');

it('build integer class by its own constructor with size less than minimum', function () {
    $integer = new Integer([
        'default' => 1254,
        'value' => 5225,
        'minLength' => 12
    ]);
    expect(function () use ($integer) {
        $integer->validateSize();
    })->toThrow(ValueException::class);
})->group('type-constructor');

it('build integer class by its own constructor with value greater than minimum', function () {
    $integer = new Integer([
        'default' => 21457,
        'value' => 2525,
        'maxLength' => 3
    ]);
    expect(function () use ($integer) {
        $integer->validateSize();
    })->toThrow(ValueException::class);
})->group('type-constructor');

/**
 * Tests using type constructor.
 */
it('build integer class by factory method', function () {
    $integer = Type::create([
        'type' => 'integer'
    ]);
    expect($integer)->toBeInstanceOf(Integer::class);
})->group('type-factory');

it('build integer class by factory method with default value', function () {
    $integer = Type::create([
        'type' => 'integer',
        'default' => 15
    ]);
    expect($integer->getValue()->getValue())->toBe('15');
    expect($integer->getValue()->__toString())->toBe('15');
})->group('type-factory');

it('build integer class by factory method with setted value', function () {
    $integer = Type::create([
        'type' => 'integer',
        'default' => 15,
        'value' => 10
    ]);
    expect($integer->getValue()->getValue())->toBe('10');
    expect($integer->getValue()->__toString())->toBe('10');
})->group('type-factory');

it('build integer class by factory method with range set', function () {
    $integer = Type::create([
        'type' => 'integer',
        'default' => 15,
        'value' => 10,
        'minimum' => 5,
        'maximum' => 20
    ]);
    expect($integer->getRange()->getMinimum())->toBe(5);
    expect($integer->getRange()->getMaximum())->toBe(20);
})->group('type-factory');

it('build integer class by factory method with size set', function () {
    $integer = Type::create([
        'type' => 'integer',
        'default' => 15,
        'value' => 10,
        'minLength' => 1,
        'maxLength' => 5
    ]);
    expect($integer->getSize()->getMinimum())->toBe(1);
    expect($integer->getSize()->getMaximum())->toBe(5);
})->group('type-factory');

it('build integer class by factory method with position set', function () {
    $integer = Type::create([
        'type' => 'integer',
        'default' => 15,
        'value' => 10,
        'start' => 50,
        'end' => 65
    ]);
    expect($integer->getPosition()->getStart())->toBe(50);
    expect($integer->getPosition()->getEnd())->toBe(65);
})->group('type-factory');

it('build integer class by factory method with alignment set', function () {
    $integer = Type::create([
        'type' => 'integer',
        'default' => 15,
        'value' => 10,
        'align' => 'right'
    ]);
    expect($integer->getAlignment())->toBe(STR_PAD_RIGHT);
})->group('type-factory');
