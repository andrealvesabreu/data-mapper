<?php

use Inspire\DataMapper\DataType\{
    Numeric,
    Type
};
use Inspire\DataMapper\Exceptions\ValueException;

/**
 * Tests using type constructor.
 */
it('build numeric class by its own constructor', function () {
    $numeric = new Numeric([]);
    expect($numeric)->toBeInstanceOf(Numeric::class);
})->group('type-constructor');

it('build numeric class by its own constructor with default value and no format', function () {
    $numeric = new Numeric([
        'default' => '0123456789'
    ]);
    expect($numeric->getValue()->getValue())->toBe('0123456789');
    expect($numeric->getValue()->__toString())->toBe('0123456789');
})->group('type-constructor');

it('build numeric class by its own constructor with default value and custom format', function () {
    $numeric = new Numeric([
        'default' => '012.345.678-9',
        'format' => '999.999.999-9'
    ]);
    expect($numeric->getValue()->getValue())->toBe('0123456789');
    expect($numeric->getValue()->__toString())->toBe('012.345.678-9');
})->group('type-constructor');

it('build numeric class by its own constructor with setted value and format', function () {
    $numeric = new Numeric([
        'default' => '000.000.000-0',
        'value' => '987.654.321-0',
        'format' => '999.999.999-9'
    ]);
    expect($numeric->getValue()->getValue())->toBe('9876543210');
    expect($numeric->getValue()->__toString())->toBe('987.654.321-0');
})->group('type-constructor');

it('build numeric class by its own constructor with size set', function () {
    $numeric = new Numeric([
        'default' => '000.000.000-0',
        'value' => '987.654.321-0',
        'format' => '999.999.999-9',
        'minLength' => 10,
        'maxLength' => 15
    ]);
    expect($numeric->getSize()->getMinimum())->toBe(10);
    expect($numeric->getSize()->getMaximum())->toBe(15);
})->group('type-constructor');

it('build numeric class by its own constructor with position set', function () {
    $numeric = new Numeric([
        'default' => '000.000.000-0',
        'value' => '987.654.321-0',
        'format' => '999.999.999-9',
        'start' => 50,
        'end' => 65
    ]);
    expect($numeric->getPosition()->getStart())->toBe(50);
    expect($numeric->getPosition()->getEnd())->toBe(65);
})->group('type-constructor');

it('build numeric class by its own constructor with alignment set', function () {
    $numeric = new Numeric([
        'default' => '000.000.000-0',
        'value' => '987.654.321-0',
        'format' => '999.999.999-9',
        'align' => 'right'
    ]);
    expect($numeric->getAlignment())->toBe(STR_PAD_RIGHT);
})->group('type-constructor');

it('build numeric class by its own constructor with size less than minimum', function () {
    $numeric = new Numeric([
        'default' => '123456',
        'value' => '123456790',
        'minLength' => 50
    ]);
    expect(function () use ($numeric) {
        $numeric->validateSize();
    })->toThrow(ValueException::class);
})->group('type-constructor');

it('build numeric class by its own constructor with value greater than minimum', function () {
    $numeric = new Numeric([
        'default' => '123456',
        'value' => '123456790',
        'maxLength' => 3
    ]);
    expect(function () use ($numeric) {
        $numeric->validateSize();
    })->toThrow(ValueException::class);
})->group('type-constructor');

/**
 * Tests using type constructor.
 */
it('build numeric class by factory method', function () {
    $numeric = Type::create([
        'type' => 'numeric',
    ]);
    expect($numeric)->toBeInstanceOf(Numeric::class);
})->group('type-factory');

it('build numeric class by factory method with default value and no format', function () {
    $numeric = Type::create([
        'type' => 'numeric',
        'default' => '1234567890'
    ]);
    expect($numeric->getValue()->getValue())->toBe('1234567890');
    expect($numeric->getValue()->__toString())->toBe('1234567890');
})->group('type-factory');

it('build numeric class by factory method with default value and custom format', function () {
    $numeric = Type::create([
        'type' => 'numeric',
        'default' => '963.258.741-5',
        'format' => '999.999.999-9'
    ]);
    expect($numeric->getValue()->getValue())->toBe('9632587415');
    expect($numeric->getValue()->__toString())->toBe('963.258.741-5');
})->group('type-factory');

it('build numeric class by factory method with setted value and format', function () {
    $numeric = Type::create([
        'type' => 'numeric',
        'default' => '001.654.789-8',
        'value' => '963.258.741-5',
        'format' => '999.999.999-9'
    ]);
    expect($numeric->getValue()->getValue())->toBe('9632587415');
    expect($numeric->getValue()->__toString())->toBe('963.258.741-5');
})->group('type-factory');

it('build numeric class by factory method with size set', function () {
    $numeric = Type::create([
        'type' => 'numeric',
        'default' => '001.654.789-8',
        'value' => '963.258.741-5',
        'format' => '999.999.999-9',
        'minLength' => 10,
        'maxLength' => 15
    ]);
    expect($numeric->getSize()->getMinimum())->toBe(10);
    expect($numeric->getSize()->getMaximum())->toBe(15);
})->group('type-factory');

it('build numeric class by factory method with position set', function () {
    $numeric = Type::create([
        'type' => 'numeric',
        'default' => '001.654.789-8',
        'value' => '963.258.741-5',
        'format' => '999.999.999-9',
        'start' => 50,
        'end' => 65
    ]);
    expect($numeric->getPosition()->getStart())->toBe(50);
    expect($numeric->getPosition()->getEnd())->toBe(65);
})->group('type-factory');

it('build numeric class by factory method with alignment set', function () {
    $numeric = Type::create([
        'type' => 'numeric',
        'default' => '001.654.789-8',
        'value' => '963.258.741-5',
        'format' => '999.999.999-9',
        'align' => 'right'
    ]);
    expect($numeric->getAlignment())->toBe(STR_PAD_RIGHT);
})->group('type-factory');
