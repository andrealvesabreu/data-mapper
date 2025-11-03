<?php

use Inspire\DataMapper\Exceptions\LimitViolationException;
use Inspire\DataMapper\Mappers\Base;

it('extract collection from number indexed collection', function () {
    $source = include dirname(__DIR__, 2) . '/models/collection_num.php';
    $data = include dirname(__DIR__, 2) . '/data/COLLECTION_ASSOC.php';
    $mappedData = Base::extract(
        $source,
        $data,
        true
    );
    expect(count($mappedData))->toBe(10000);
})->group('collection-extraction');

it('extract collection from key indexed collection', function () {
    $source = include dirname(__DIR__, 2) . '/models/collection_assoc.php';
    $data = include dirname(__DIR__, 2) . '/data/COLLECTION_ASSOC.php';
    $mappedData = Base::extract(
        $source,
        $data,
        true
    );
    expect(count($mappedData))->toBe(10000);
})->group('collection-extraction');

it('extract collection from number indexed collection max items', function () {
    $source = include dirname(__DIR__, 2) . '/models/collection_num.php';
    $data = include dirname(__DIR__, 2) . '/data/COLLECTION_ASSOC.php';
    $source['setup']['maxItems'] = 10;
    expect(function () use ($source, $data) {
        Base::extract(
            $source,
            $data,
            true
        );
    })->toThrow(LimitViolationException::class);
})->group('collection-extraction');

it('extract collection from key indexed collection max items', function () {
    $source = include dirname(__DIR__, 2) . '/models/collection_assoc.php';
    $data = include dirname(__DIR__, 2) . '/data/COLLECTION_ASSOC.php';
    $source['setup']['maxItems'] = 10;
    expect(function () use ($source, $data) {
        Base::extract(
            $source,
            $data,
            true
        );
    })->toThrow(LimitViolationException::class);
})->group('collection-extraction');
