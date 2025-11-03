<?php

use Inspire\DataMapper\DataType\{
    Enum,
    Type
};
use Inspire\DataMapper\Exceptions\ValueException;

/**
 * Tests using type constructor.
 */
it('build enum class by its own constructor', function () {
    $enum = new Enum([]);
    expect($enum)->toBeInstanceOf(Enum::class);
})->group('type-constructor');

it('build enum class by its own constructor with default value', function () {
    $enum = new Enum(
        [
            'default' => 15,
            'enum' => [
                5,
                10,
                15,
                20
            ]
        ]
    );
    expect($enum->getValue()->getValue())->toBe('15');
    expect($enum->getValue()->__toString())->toBe('15');
})->group('type-constructor');

it('build enum class by its own constructor with setted value', function () {
    $enum = new Enum([
        'default' => 15,
        'value' => 10,
        'enum' => [
            5,
            10,
            15,
            20
        ]
    ]);
    expect($enum->getValue()->getValue())->toBe('10');
    expect($enum->getValue()->__toString())->toBe('10');
})->group('type-constructor');

it('build enum class by its own constructor with position set', function () {
    $enum = new Enum([
        'default' => 15,
        'value' => 10,
        'enum' => [
            5,
            10,
            15,
            20
        ],
        'start' => 50,
        'end' => 65
    ]);
    expect($enum->getPosition()->getStart())->toBe(50);
    expect($enum->getPosition()->getEnd())->toBe(65);
})->group('type-constructor');

it('build enum class by its own constructor with alignment set', function () {
    $enum = new Enum([
        'default' => 15,
        'value' => 10,
        'enum' => [
            5,
            10,
            15,
            20
        ],
        'align' => 'right'
    ]);
    expect($enum->getAlignment())->toBe(STR_PAD_RIGHT);
})->group('type-constructor');

it('build enum class by its own constructor with an invalid value', function () {
    $enum = new Enum([
        'default' => 15,
        'value' => 11,
        'enum' => [
            5,
            10,
            15,
            20
        ]
    ]);
    expect(function () use ($enum) {
        $enum->isAllowedValue();
    })->toThrow(ValueException::class);
})->group('type-constructor');

/**
 * Tests using type constructor.
 */
it('build enum class by factory method', function () {
    $enum = Type::create([
        'type' => 'enum'
    ]);
    expect($enum)->toBeInstanceOf(Enum::class);
})->group('type-factory');

it('build enum class by factory method with default value', function () {
    $enum = Type::create([
        'type' => 'enum',
        'default' => 15
    ]);
    expect($enum->getValue()->getValue())->toBe('15');
    expect($enum->getValue()->__toString())->toBe('15');
})->group('type-factory');

it('build enum class by factory method with setted value', function () {
    $enum = Type::create([
        'type' => 'enum',
        'default' => 15,
        'value' => 10,
        'enum' => [
            5,
            10,
            15,
            20
        ]
    ]);
    expect($enum->getValue()->getValue())->toBe('10');
    expect($enum->getValue()->__toString())->toBe('10');
})->group('type-factory');

it('build enum class by factory method with position set', function () {
    $enum = Type::create([
        'type' => 'enum',
        'default' => 15,
        'value' => 10,
        'enum' => [
            5,
            10,
            15,
            20
        ],
        'start' => 50,
        'end' => 65
    ]);
    expect($enum->getPosition()->getStart())->toBe(50);
    expect($enum->getPosition()->getEnd())->toBe(65);
})->group('type-factory');

it('build enum class by factory method with alignment set', function () {
    $enum = Type::create([
        'type' => 'enum',
        'default' => 15,
        'value' => 10,
        'enum' => [
            5,
            10,
            15,
            20
        ],
        'align' => 'right'
    ]);
    expect($enum->getAlignment())->toBe(STR_PAD_RIGHT);
})->group('type-factory');
