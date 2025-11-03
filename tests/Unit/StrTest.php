<?php

use Inspire\DataMapper\DataType\{
    Str,
    Type
};
use Inspire\DataMapper\Exceptions\ValueException;

/**
 * Tests using type constructor.
 */
it('build str class by its own constructor', function () {
    $str = new Str([]);
    expect($str)->toBeInstanceOf(Str::class);
})->group('type-constructor');

it('build str class by its own constructor with default value and no format', function () {
    $str = new Str([
        'default' => '0123456789'
    ]);
    expect($str->getValue()->getValue())->toBe('0123456789');
    expect($str->getValue()->__toString())->toBe('0123456789');
})->group('type-constructor');

it('build str class by its own constructor with default value and custom format', function () {
    $str = new Str([
        'default' => 'ABC-1D34',
        'format' => 'AAA-9A99'
    ]);
    expect($str->getValue()->getValue())->toBe('ABC1D34');
    expect($str->getValue()->__toString())->toBe('ABC-1D34');
})->group('type-constructor');

it('build str class by its own constructor with setted value and format', function () {
    $str = new Str([
        'default' => 'ABC-1D34',
        'value' => 'ZZZ-8976',
        'format' => 'AAA-9A99'
    ]);
    expect($str->getValue()->getValue())->toBe('ZZZ8976');
    expect($str->getValue()->__toString())->toBe('ZZZ-8976');
})->group('type-constructor');

it('build str class by its own constructor with size set', function () {
    $str = new Str([
        'default' => 'ABC-1D34',
        'value' => 'ZZZ-8976',
        'format' => 'AAA-9A99',
        'minLength' => 10,
        'maxLength' => 15
    ]);
    expect($str->getSize()->getMinimum())->toBe(10);
    expect($str->getSize()->getMaximum())->toBe(15);
})->group('type-constructor');

it('build str class by its own constructor with position set', function () {
    $str = new Str([
        'default' => 'ABC-1D34',
        'value' => 'ZZZ-8976',
        'format' => 'AAA-9A99',
        'start' => 50,
        'end' => 65
    ]);
    expect($str->getPosition()->getStart())->toBe(50);
    expect($str->getPosition()->getEnd())->toBe(65);
})->group('type-constructor');

it('build str class by its own constructor with alignment set', function () {
    $str = new Str([
        'default' => 'ABC-1D34',
        'value' => 'ZZZ-8976',
        'format' => 'AAA-9A99',
        'align' => 'right'
    ]);
    expect($str->getAlignment())->toBe(STR_PAD_RIGHT);
})->group('type-constructor');

it('build str class by its own constructor with size less than minimum', function () {
    $str = new Str([
        'default' => 'test',
        'value' => 'Doing another test',
        'minLength' => 50
    ]);
    expect(function () use ($str) {
        $str->validateSize();
    })->toThrow(ValueException::class);
})->group('type-constructor');

it('build str class by its own constructor with value greater than minimum', function () {
    $str = new Str([
        'default' => 'test',
        'value' => 'One last test',
        'maxLength' => 3
    ]);
    expect(function () use ($str) {
        $str->validateSize();
    })->toThrow(ValueException::class);
})->group('type-constructor');

/**
 * Tests using type constructor.
 */
it('build str class by factory method', function () {
    $str = Type::create([
        'type' => 'string',
    ]);
    expect($str)->toBeInstanceOf(Str::class);
})->group('type-factory');

it('build str class by factory method with default value and no format', function () {
    $str = Type::create([
        'type' => 'string',
        'default' => '1234567890'
    ]);
    expect($str->getValue()->getValue())->toBe('1234567890');
    expect($str->getValue()->__toString())->toBe('1234567890');
})->group('type-factory');

it('build str class by factory method with default value and custom format', function () {
    $str = Type::create([
        'type' => 'string',
        'default' => 'TTT-9028',
        'format' => 'AAA-9A99'
    ]);
    expect($str->getValue()->getValue())->toBe('TTT9028');
    expect($str->getValue()->__toString())->toBe('TTT-9028');
})->group('type-factory');

it('build str class by factory method with setted value and format', function () {
    $str = Type::create([
        'type' => 'string',
        'default' => 'NNN-6542',
        'value' => 'TTT-9028',
        'format' => 'AAA-9A99'
    ]);
    expect($str->getValue()->getValue())->toBe('TTT9028');
    expect($str->getValue()->__toString())->toBe('TTT-9028');
})->group('type-factory');

it('build str class by factory method with size set', function () {
    $str = Type::create([
        'type' => 'string',
        'default' => 'NNN-6542',
        'value' => 'TTT-9028',
        'format' => 'AAA-9A99',
        'minLength' => 10,
        'maxLength' => 15
    ]);
    expect($str->getSize()->getMinimum())->toBe(10);
    expect($str->getSize()->getMaximum())->toBe(15);
})->group('type-factory');

it('build str class by factory method with position set', function () {
    $str = Type::create([
        'type' => 'string',
        'default' => 'NNN-6542',
        'value' => 'TTT-9028',
        'format' => 'AAA-9A99',
        'start' => 50,
        'end' => 65
    ]);
    expect($str->getPosition()->getStart())->toBe(50);
    expect($str->getPosition()->getEnd())->toBe(65);
})->group('type-factory');

it('build str class by factory method with alignment set', function () {
    $str = Type::create([
        'type' => 'string',
        'default' => 'NNN-6542',
        'value' => 'TTT-9028',
        'format' => 'AAA-9A99',
        'align' => 'right'
    ]);
    expect($str->getAlignment())->toBe(STR_PAD_RIGHT);
})->group('type-factory');
