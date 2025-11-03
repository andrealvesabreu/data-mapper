<?php

use Inspire\DataMapper\Exceptions\LimitViolationException;
use Inspire\DataMapper\Mappers\Base;

it('extract notfis', function () {
    $source = include dirname(__DIR__, 2) . '/models/notfis_proceda_31.php';
    $data = file(dirname(__DIR__, 2) . '/data/notfis_proceda_31.txt');
    $convertedData = Base::extract(
        $source,
        $data
    );
    expect(count($convertedData))->toBe(11);
})->group('notfis-extraction');

it('extract notfis max items', function () {
    $source = include dirname(__DIR__, 2) . '/models/notfis_proceda_31.php';
    $data = file(dirname(__DIR__, 2) . '/data/notfis_proceda_31.txt');
    $source['setup']['maxItems'] = 10;
    expect(function () use ($source, $data) {
        Base::extract(
            $source,
            $data,
            true
        );
    })->toThrow(LimitViolationException::class);
})->group('notfis-extraction');
