<?php

use Inspire\DataMapper\Mappers\Base;

it('convert xml to csv assoc', function () {
    $source = include dirname(__DIR__, 2) . '/models/xml.php';
    $target = include dirname(__DIR__, 2) . '/models/csv_assoc2.php';
    $data = dirname(__DIR__, 2) . '/data/INPUT.xml';
    $xmlData = Base::convert(
        $source,
        $target,
        $data
    );
    expect(count(Base::getMappedData()))->toBe(281);
    expect(substr_count(trim($xmlData), PHP_EOL))->toBe(281);
})->group('xml-to-others');

it('convert xml to csv num', function () {
    $source = include dirname(__DIR__, 2) . '/models/xml.php';
    $target = include dirname(__DIR__, 2) . '/models/csv_num2.php';
    $data = dirname(__DIR__, 2) . '/data/INPUT.xml';
    $xmlData = Base::convert(
        $source,
        $target,
        $data
    );
    expect(count(Base::getMappedData()))->toBe(281);
    expect(substr_count(trim($xmlData), PHP_EOL))->toBe(281);
})->group('xml-to-others');

it('convert xml to xml', function () {
    $source = include dirname(__DIR__, 2) . '/models/xml.php';
    $target = include dirname(__DIR__, 2) . '/models/xml.php';
    $data = dirname(__DIR__, 2) . '/data/INPUT.xml';
    $xmlData = Base::convert(
        $source,
        $target,
        $data
    );
    $xmlData = json_decode($xmlData);
    expect(count(Base::getMappedData()))->toBe(281);
    expect(count($xmlData))->toBe(281);
})->group('xml-to-others');

it('convert xml to json', function () {
    $source = include dirname(__DIR__, 2) . '/models/xml.php';
    $target = include dirname(__DIR__, 2) . '/models/json.php';
    $data = dirname(__DIR__, 2) . '/data/INPUT.xml';
    $xmlData = Base::convert(
        $source,
        $target,
        $data
    );
    $xmlData = json_decode($xmlData);
    expect(count(Base::getMappedData()))->toBe(281);
    expect(count($xmlData))->toBe(281);
})->group('xml-to-others');

it('convert xml to collection assoc', function () {
    $source = include dirname(__DIR__, 2) . '/models/xml.php';
    $target = include dirname(__DIR__, 2) . '/models/collection_assoc.php';
    $data = dirname(__DIR__, 2) . '/data/INPUT.xml';
    $collectionData = Base::convert(
        $source,
        $target,
        $data
    );
    $collectionData = json_decode($collectionData, true);
    expect(count(Base::getMappedData()))->toBe(281);
    expect(count($collectionData))->toBe(281);
})->group('xml-to-others');

it('convert xml to collection num', function () {
    $source = include dirname(__DIR__, 2) . '/models/xml.php';
    $target = include dirname(__DIR__, 2) . '/models/collection_num.php';
    $data = dirname(__DIR__, 2) . '/data/INPUT.xml';
    $collectionData = Base::convert(
        $source,
        $target,
        $data
    );
    $collectionData = json_decode($collectionData, true);
    expect(count(Base::getMappedData()))->toBe(281);
    expect(count($collectionData))->toBe(281);
})->group('xml-to-others');

it('convert xml to proceda notfis', function () {
    $source = include dirname(__DIR__, 2) . '/models/xml.php';
    $target = include dirname(__DIR__, 2) . '/models/notfis_proceda_31.php';
    $data = dirname(__DIR__, 2) . '/data/INPUT.xml';
    $collectionData = Base::convert(
        $source,
        $target,
        $data
    );
    $collectionData = json_decode($collectionData, true);
    expect(count(Base::getMappedData()))->toBe(281);
    expect(count($collectionData))->toBe(1124);
})->group('xml-to-others');
