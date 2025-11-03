<?php

use Inspire\DataMapper\DataType\{
    Datetime,
    Type
};
use Inspire\DataMapper\Exceptions\ValueException;

/**
 * Tests using type constructor.
 */
it('build datetime class by its own constructor', function () {
    $datetime = new Datetime(['nulladle' => false]);
    expect($datetime)->toBeInstanceOf(Datetime::class);
})->group('type-constructor');

it('build datetime class by its own constructor with default value and format', function () {
    $datetime = new Datetime([
        'default' => '2025-04-02'
    ]);
    expect(date('Y-m-d', intval($datetime->getValue()->getValue())))->toBe('2025-04-02');
    expect($datetime->getValue()->__toString())->toBe('2025-04-02');
})->group('type-constructor');

it('build datetime class by its own constructor with default value and custom format', function () {
    $datetime = new Datetime([
        'default' => '25-04-02',
        'format' => 'yy-mm-dd'
    ]);
    expect(date('Y-m-d', intval($datetime->getValue()->getValue())))->toBe('2025-04-02');
    expect($datetime->getValue()->__toString())->toBe('25-04-02');
})->group('type-constructor');

it('build datetime class by its own constructor with setted value and default format', function () {
    $datetime = new Datetime([
        'default' => '2025-04-01',
        'value' => '2025-04-12'
    ]);
    expect(date('Y-m-d', intval($datetime->getValue()->getValue())))->toBe('2025-04-12');
    expect($datetime->getValue()->__toString())->toBe('2025-04-12');
})->group('type-constructor');

it('build datetime class by its own constructor with setted value and format', function () {
    $datetime = new Datetime([
        'default' => '25-04-01',
        'value' => '25-04-12',
        'format' => 'yy-mm-dd'
    ]);
    expect(date('Y-m-d', intval($datetime->getValue()->getValue())))->toBe('2025-04-12');
    expect($datetime->getValue()->__toString())->toBe('25-04-12');
})->group('type-constructor');

it('build datetime class by its own constructor with range set', function () {
    $datetime = new Datetime([
        'default' => '2025-04-01',
        'value' => '2025-04-12',
        'format' => 'yyyy-mm-dd',
        'minimum' => '2025-01-01',
        'maximum' => '2025-10-01'
    ]);
    expect($datetime->getRange()->getMinimum())->toBe(strtotime('2025-01-01'));
    expect($datetime->getRange()->getMaximum())->toBe(strtotime('2025-10-01'));
})->group('type-constructor');

it('build datetime class by its own constructor with size set', function () {
    $datetime = new Datetime([
        'default' => '2025-04-03',
        'value' => '2025-04-12',
        'format' => 'yyyy-mm-dd',
        'minLength' => 10,
        'maxLength' => 15
    ]);
    expect($datetime->getSize()->getMinimum())->toBe(10);
    expect($datetime->getSize()->getMaximum())->toBe(15);
})->group('type-constructor');

it('build datetime class by its own constructor with position set', function () {
    $datetime = new Datetime([
        'default' => '2025-04-03',
        'value' => '2025-04-12',
        'format' => 'yyyy-mm-dd',
        'start' => 50,
        'end' => 65
    ]);
    expect($datetime->getPosition()->getStart())->toBe(50);
    expect($datetime->getPosition()->getEnd())->toBe(65);
})->group('type-constructor');

it('build datetime class by its own constructor with alignment set', function () {
    $datetime = new Datetime([
        'default' => '2025-04-03',
        'value' => '2025-04-12',
        'format' => 'yyyy-mm-dd',
        'align' => 'right'
    ]);
    expect($datetime->getAlignment())->toBe(STR_PAD_RIGHT);
})->group('type-constructor');

it('build datetime class by its own constructor with value less than range limit', function () {
    $datetime = new Datetime([
        'default' => '2025-04-03',
        'value' => '2025-04-12',
        'format' => 'yyyy-mm-dd',
        'minimum' => '2025-05-01'
    ]);
    expect(function () use ($datetime) {
        $datetime->validateRange();
    })->toThrow(ValueException::class);
})->group('type-constructor');

it('build datetime class by its own constructor with value greater than range limit', function () {
    $datetime = new Datetime([
        'default' => '2025-04-03',
        'value' => '2025-04-12',
        'format' => 'yyyy-mm-dd',
        'maximum' => '2025-04-01'
    ]);
    expect(function () use ($datetime) {
        $datetime->validateRange();
    })->toThrow(ValueException::class);
})->group('type-constructor');

it('build datetime class by its own constructor with size less than minimum', function () {
    $datetime = new Datetime([
        'default' => '2025-04-03',
        'value' => '2025-04-12',
        'format' => 'yyyy-mm-dd',
        'minLength' => 12
    ]);
    expect(function () use ($datetime) {
        $datetime->validateSize();
    })->toThrow(ValueException::class);
})->group('type-constructor');

it('build datetime class by its own constructor with value greater than minimum', function () {
    $datetime = new Datetime([
        'default' => '2025-04-03',
        'value' => '2025-04-12',
        'format' => 'yyyy-mm-dd',
        'maxLength' => 8
    ]);
    expect(function () use ($datetime) {
        $datetime->validateSize();
    })->toThrow(ValueException::class);
})->group('type-constructor');

/**
 * Tests using type constructor.
 */
it('build datetime class by factory method', function () {
    $datetime = Type::create([
        'type' => 'datetime',
    ]);
    expect($datetime)->toBeInstanceOf(Datetime::class);
})->group('type-factory');

it('build datetime class by factory method with default value and format', function () {
    $datetime = Type::create([
        'type' => 'datetime',
        'default' => '2025-04-02'
    ]);
    expect(date('Y-m-d', intval($datetime->getValue()->getValue())))->toBe('2025-04-02');
    expect($datetime->getValue()->__toString())->toBe('2025-04-02');
})->group('type-factory');

it('build datetime class by factory method with default value and custom format', function () {
    $datetime = Type::create([
        'type' => 'datetime',
        'default' => '25-04-02',
        'format' => 'yy-mm-dd'
    ]);
    expect(date('Y-m-d', intval($datetime->getValue()->getValue())))->toBe('2025-04-02');
    expect($datetime->getValue()->__toString())->toBe('25-04-02');
})->group('type-factory');

it('build datetime class by factory method with setted value and default format', function () {
    $datetime = Type::create([
        'type' => 'datetime',
        'default' => '2025-04-01',
        'value' => '2025-04-12'
    ]);
    expect(date('Y-m-d', intval($datetime->getValue()->getValue())))->toBe('2025-04-12');
    expect($datetime->getValue()->__toString())->toBe('2025-04-12');
})->group('type-factory');

it('build datetime class by factory method with setted value and format', function () {
    $datetime = Type::create([
        'type' => 'datetime',
        'default' => '25-04-01',
        'value' => '25-04-12',
        'format' => 'yy-mm-dd'
    ]);
    expect(date('Y-m-d', intval($datetime->getValue()->getValue())))->toBe('2025-04-12');
    expect($datetime->getValue()->__toString())->toBe('25-04-12');
})->group('type-factory');

it('build datetime class by factory method with range set', function () {
    $datetime = Type::create([
        'type' => 'datetime',
        'default' => '2025-04-01',
        'value' => '2025-04-12',
        'format' => 'yyyy-mm-dd',
        'minimum' => '2025-01-01',
        'maximum' => '2025-10-01'
    ]);
    expect($datetime->getRange()->getMinimum())->toBe(strtotime('2025-01-01'));
    expect($datetime->getRange()->getMaximum())->toBe(strtotime('2025-10-01'));
})->group('type-factory');

it('build datetime class by factory method with size set', function () {
    $datetime = Type::create([
        'type' => 'datetime',
        'default' => '2025-04-03',
        'value' => '2025-04-12',
        'format' => 'yyyy-mm-dd',
        'minLength' => 10,
        'maxLength' => 15
    ]);
    expect($datetime->getSize()->getMinimum())->toBe(10);
    expect($datetime->getSize()->getMaximum())->toBe(15);
})->group('type-factory');

it('build datetime class by factory method with position set', function () {
    $datetime = Type::create([
        'type' => 'datetime',
        'default' => '2025-04-03',
        'value' => '2025-04-12',
        'format' => 'yyyy-mm-dd',
        'start' => 50,
        'end' => 65
    ]);
    expect($datetime->getPosition()->getStart())->toBe(50);
    expect($datetime->getPosition()->getEnd())->toBe(65);
})->group('type-factory');

it('build datetime class by factory method with alignment set', function () {
    $datetime = Type::create([
        'type' => 'datetime',
        'default' => '2025-04-03',
        'value' => '2025-04-12',
        'format' => 'yyyy-mm-dd',
        'align' => 'right'
    ]);
    expect($datetime->getAlignment())->toBe(STR_PAD_RIGHT);
})->group('type-factory');
