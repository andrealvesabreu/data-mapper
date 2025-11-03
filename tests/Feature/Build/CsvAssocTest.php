<?php

use Inspire\DataMapper\Mappers\Base;

it('convert csv assoc to csv assoc', function () {
    $source = include dirname(__DIR__, 2) . '/models/csv_assoc.php';
    $target = include dirname(__DIR__, 2) . '/models/csv_assoc2.php';
    $data = dirname(__DIR__, 2) . '/data/INPUT.csv';
    $csvData = Base::convert(
        $source,
        $target,
        $data
    );
    expect(count(Base::getMappedData()))->toBe(10000);
    expect(substr_count(trim($csvData), PHP_EOL))->toBe(10000);
})->group('csv-assoc-to-others');

it('convert csv assoc to csv num', function () {
    $source = include dirname(__DIR__, 2) . '/models/csv_assoc.php';
    $target = include dirname(__DIR__, 2) . '/models/csv_num.php';
    $data = dirname(__DIR__, 2) . '/data/INPUT.csv';
    $csvData = Base::convert(
        $source,
        $target,
        $data
    );
    expect(count(Base::getMappedData()))->toBe(10000);
    expect(substr_count(trim($csvData), PHP_EOL))->toBe(10000);
})->group('csv-assoc-to-others');

it('convert csv assoc to xml', function () {
    $source = include dirname(__DIR__, 2) . '/models/csv_assoc.php';
    $target = include dirname(__DIR__, 2) . '/models/xml.php';
    $data = dirname(__DIR__, 2) . '/data/INPUT.csv';
    $collectionData = Base::convert(
        $source,
        $target,
        $data
    );
    $collectionData = json_decode($collectionData, true);
    expect(count(Base::getMappedData()))->toBe(10000);
    expect(count($collectionData))->toBe(10000);
})->group('csv-assoc-to-others');

it('convert csv assoc to json', function () {
    $source = include dirname(__DIR__, 2) . '/models/csv_assoc.php';
    $target = include dirname(__DIR__, 2) . '/models/json.php';
    $data = dirname(__DIR__, 2) . '/data/INPUT.csv';
    $jsonData = Base::convert(
        $source,
        $target,
        $data
    );
    $jsonData = json_decode($jsonData, true);
    expect(count(Base::getMappedData()))->toBe(10000);
    expect(count($jsonData))->toBe(10000);
})->group('csv-assoc-to-others');


it('convert csv assoc to collection assoc', function () {
    $source = include dirname(__DIR__, 2) . '/models/csv_assoc.php';
    $target = include dirname(__DIR__, 2) . '/models/collection_assoc.php';
    $data = dirname(__DIR__, 2) . '/data/INPUT.csv';
    $collectionData = Base::convert(
        $source,
        $target,
        $data
    );
    $collectionData = json_decode($collectionData, true);
    expect(count(Base::getMappedData()))->toBe(10000);
    expect(count($collectionData))->toBe(10000);
})->group('csv-assoc-to-others');

it('convert csv assoc to collection num', function () {
    $source = include dirname(__DIR__, 2) . '/models/csv_assoc.php';
    $target = include dirname(__DIR__, 2) . '/models/collection_num.php';
    $data = dirname(__DIR__, 2) . '/data/INPUT.csv';
    $collectionData = Base::convert(
        $source,
        $target,
        $data
    );
    $collectionData = json_decode($collectionData, true);
    expect(count(Base::getMappedData()))->toBe(10000);
    expect(count($collectionData))->toBe(10000);
})->group('csv-assoc-to-others');

it('convert csv assoc to proceda notfis', function () {
    $source = include dirname(__DIR__, 2) . '/models/csv_assoc.php';
    $target = include dirname(__DIR__, 2) . '/models/notfis_proceda_31.php';
    $data = dirname(__DIR__, 2) . '/data/INPUT.csv';
    $collectionData = Base::convert(
        $source,
        $target,
        $data
    );
    $collectionData = json_decode($collectionData, true);
    expect(count(Base::getMappedData()))->toBe(10000);
    expect(count($collectionData))->toBe(40000);
})->group('csv-assoc-to-others');
