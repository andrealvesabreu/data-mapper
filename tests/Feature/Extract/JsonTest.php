<?php

use Inspire\DataMapper\Exceptions\LimitViolationException;
use Inspire\DataMapper\Mappers\Base;

it('extract json', function () {
    $source = include dirname(__DIR__, 2) . '/models/json.php';
    $data = json_decode(file_get_contents(dirname(__DIR__, 2) . '/data/INPUT.json'), true);
    $convertedData = Base::extract(
        $source,
        $data,
        true
    );
    expect(count($convertedData))->toBe(5379);
})->group('json-extraction');

it('extract json max items', function () {
    $source = include dirname(__DIR__, 2) . '/models/json.php';
    $data = json_decode(file_get_contents(dirname(__DIR__, 2) . '/data/INPUT.json'), true);
    $source['setup']['maxItems'] = 1;
    expect(function () use ($source, $data) {
        Base::extract(
            $source,
            $data,
            true
        );
    })->toThrow(LimitViolationException::class);
})->group('json-extraction');
