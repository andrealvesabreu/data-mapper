<?php

use Inspire\DataMapper\DataType\{
    Decimal,
    Type
};
use Inspire\DataMapper\Exceptions\ValueException;

/**
 * Tests using type constructor.
 */
it('build decimal class by its own constructor', function () {
    $decimal = new Decimal([]);
    expect($decimal)->toBeInstanceOf(Decimal::class);
})->group('type-constructor');

it('build decimal class by its own constructor with default value', function () {
    $decimal = new Decimal([
        'default' => 15.62,
        'dec_sep' => '.',
        'decimals' => 2
    ]);
    expect($decimal->getValue()->getValue())->toBe('15.62');
    expect($decimal->getValue()->__toString())->toBe('15.62');
})->group('type-constructor');

it('build decimal class by its own constructor with setted value', function () {
    $decimal = new Decimal([
        'default' => 15,
        'value' => 10.36,
        'dec_sep' => '.',
        'decimals' => 2
    ]);
    expect($decimal->getValue()->getValue())->toBe('10.36');
    expect($decimal->getValue()->__toString())->toBe('10.36');
})->group('type-constructor');

it('build decimal class by its own constructor with range set', function () {
    $decimal = new Decimal([
        'default' => 15,
        'value' => 10.41,
        'minimum' => 1.23,
        'maximum' => 20.5,
        'dec_sep' => '.',
        'decimals' => 2
    ]);
    expect($decimal->getRange()->getMinimum())->toBe(1.23);
    expect($decimal->getRange()->getMaximum())->toBe(20.5);
})->group('type-constructor');

it('build decimal class by its own constructor with size set', function () {
    $decimal = new Decimal([
        'default' => 15,
        'value' => 10,
        'minLength' => 1,
        'maxLength' => 5,
        'dec_sep' => '.',
        'decimals' => 2
    ]);
    expect($decimal->getSize()->getMinimum())->toBe(1);
    expect($decimal->getSize()->getMaximum())->toBe(5);
})->group('type-constructor');

it('build decimal class by its own constructor with position set', function () {
    $decimal = new Decimal([
        'default' => 15,
        'value' => 10,
        'start' => 50,
        'end' => 65,
        'dec_sep' => '.',
        'decimals' => 2
    ]);
    expect($decimal->getPosition()->getStart())->toBe(50);
    expect($decimal->getPosition()->getEnd())->toBe(65);
})->group('type-constructor');

it('build decimal class by its own constructor with alignment set', function () {
    $decimal = new Decimal([
        'default' => 15,
        'value' => 10,
        'align' => 'right',
        'dec_sep' => '.',
        'decimals' => 2
    ]);
    expect($decimal->getAlignment())->toBe(STR_PAD_RIGHT);
})->group('type-constructor');

it('build decimal class by its own constructor with value less than range limit', function () {
    $decimal = new Decimal([
        'default' => 12.857,
        'value' => 16.58,
        'minimum' => 20,
        'dec_sep' => '.',
        'decimals' => 2
    ]);
    expect(function () use ($decimal) {
        $decimal->validateRange();
    })->toThrow(ValueException::class);
})->group('type-constructor');

it('build decimal class by its own constructor with value greater than range limit', function () {
    $decimal = new Decimal([
        'default' => 6.24,
        'value' => 56.52,
        'maximum' => 30,
        'dec_sep' => '.',
        'decimals' => 2
    ]);
    expect(function () use ($decimal) {
        $decimal->validateRange();
    })->toThrow(ValueException::class);
})->group('type-constructor');

it('build decimal class by its own constructor with size less than minimum', function () {
    $decimal = new Decimal([
        'default' => 12.54,
        'value' => 52.25,
        'minLength' => 12,
        'dec_sep' => '.',
        'decimals' => 2
    ]);
    expect(function () use ($decimal) {
        $decimal->validateSize();
    })->toThrow(ValueException::class);
})->group('type-constructor');

it('build decimal class by its own constructor with value greater than minimum', function () {
    $decimal = new Decimal([
        'default' => 21.457,
        'value' => 25.25,
        'maxLength' => 3,
        'dec_sep' => '.',
        'decimals' => 2
    ]);
    expect(function () use ($decimal) {
        $decimal->validateSize();
    })->toThrow(ValueException::class);
})->group('type-constructor');

/**
 * Tests using type constructor.
 */
it('build decimal class by factory method', function () {
    $decimal = Type::create([
        'type' => 'decimal'
    ]);
    expect($decimal)->toBeInstanceOf(Decimal::class);
})->group('type-factory');

it('build decimal class by factory method with default value', function () {
    $decimal = Type::create([
        'type' => 'decimal',
        'default' => 52.47,
        'dec_sep' => '.',
        'decimals' => 2
    ]);
    expect($decimal->getValue()->getValue())->toBe('52.47');
    expect($decimal->getValue()->__toString())->toBe('52.47');
})->group('type-factory');

it('build decimal class by factory method with setted value', function () {
    $decimal = Type::create([
        'type' => 'decimal',
        'default' => 52.47,
        'value' => 12.58,
        'dec_sep' => '.',
        'decimals' => 2
    ]);
    expect($decimal->getValue()->getValue())->toBe('12.58');
    expect($decimal->getValue()->__toString())->toBe('12.58');
})->group('type-factory');

it('build decimal class by factory method with range set', function () {
    $decimal = Type::create([
        'type' => 'decimal',
        'default' => 541.45,
        'value' => 85.64,
        'minimum' => 12.654,
        'maximum' => 254.124
    ]);
    expect($decimal->getRange()->getMinimum())->toBe(12.654);
    expect($decimal->getRange()->getMaximum())->toBe(254.124);
})->group('type-factory');

it('build decimal class by factory method with size set', function () {
    $decimal = Type::create([
        'type' => 'decimal',
        'default' => 15,
        'value' => 548.41,
        'minLength' => 1,
        'maxLength' => 6
    ]);
    expect($decimal->getSize()->getMinimum())->toBe(1);
    expect($decimal->getSize()->getMaximum())->toBe(6);
})->group('type-factory');

it('build decimal class by factory method with position set', function () {
    $decimal = Type::create([
        'type' => 'decimal',
        'default' => 124.56,
        'value' => 563.23,
        'start' => 5,
        'end' => 120
    ]);
    expect($decimal->getPosition()->getStart())->toBe(5);
    expect($decimal->getPosition()->getEnd())->toBe(120);
})->group('type-factory');

it('build decimal class by factory method with alignment set', function () {
    $decimal = Type::create([
        'type' => 'decimal',
        'default' => 15,
        'value' => 1254.45,
        'align' => 'right'
    ]);
    expect($decimal->getAlignment())->toBe(STR_PAD_RIGHT);
})->group('type-factory');
