<?php

use Inspire\DataMapper\Exceptions\LimitViolationException;
use Inspire\DataMapper\Mappers\Base;

it('extract csv from number indexed csv', function () {
    $source = include dirname(__DIR__, 2) . '/models/csv_num.php';
    $data = file(dirname(__DIR__, 2) . '/data/INPUT.csv');
    $mappedData = Base::extract(
        $source,
        $data,
        true
    );
    expect(count($mappedData))->toBe(10000);
})->group('csv-extraction');

it('extract csv from key indexed csv', function () {
    $source = include dirname(__DIR__, 2) . '/models/csv_assoc.php';
    $data = file(dirname(__DIR__, 2) . '/data/INPUT.csv');
    $mappedData = Base::extract(
        $source,
        $data,
        true
    );
    expect(count($mappedData))->toBe(10000);
})->group('csv-extraction');

it('extract csv from number indexed csv max items', function () {
    $source = include dirname(__DIR__, 2) . '/models/csv_num.php';
    $data = file(dirname(__DIR__, 2) . '/data/INPUT.csv');
    $source['setup']['maxItems'] = 10;
    expect(function () use ($source, $data) {
        $mappedData = Base::extract(
            $source,
            $data,
            true
        );
    })->toThrow(LimitViolationException::class);
})->group('csv-extraction');

it('extract csv from key indexed csv max items', function () {
    $source = include dirname(__DIR__, 2) . '/models/csv_assoc.php';
    $data = file(dirname(__DIR__, 2) . '/data/INPUT.csv');
    $source['setup']['maxItems'] = 10;
    expect(function () use ($source, $data) {
        $mappedData = Base::extract(
            $source,
            $data,
            true
        );
    })->toThrow(LimitViolationException::class);
})->group('csv-extraction');
