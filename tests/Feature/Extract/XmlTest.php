<?php

use Inspire\DataMapper\Exceptions\LimitViolationException;
use Inspire\DataMapper\Mappers\Base;

it('extract xml', function () {
    $source = include dirname(__DIR__, 2) . '/models/xml.php';
    $data = dirname(__DIR__, 2) . '/data/INPUT.xml';
    $convertedData = Base::extract(
        $source,
        $data
    );
    expect(count($convertedData))->toBe(281); //282
})->group('xml-extraction');

it('extract xml max items', function () {
    $source = include dirname(__DIR__, 2) . '/models/xml.php';
    $data = dirname(__DIR__, 2) . '/data/INPUT.xml';
    $source['setup']['maxItems'] = 10;
    expect(function () use ($source, $data) {
        Base::extract(
            $source,
            $data,
            true
        );
    })->toThrow(LimitViolationException::class);
})->group('xml-extraction');
