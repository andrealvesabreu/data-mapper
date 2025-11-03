<?php

use Inspire\DataMapper\Mappers\Base;

it('convert proceda notfis to csv assoc', function () {
    $source = include dirname(__DIR__, 2) . '/models/notfis_proceda_31.php';
    $target = include dirname(__DIR__, 2) . '/models/csv_assoc2.php';
    $data = dirname(__DIR__, 2) . '/data/notfis_proceda_31.txt';
    $notfisData = Base::convert(
        $source,
        $target,
        $data
    );
    expect(count(Base::getMappedData()))->toBe(11);
    expect(substr_count(trim($notfisData), PHP_EOL))->toBe(11);
})->group('proceda-notfis-to-others');

it('convert proceda notfis to csv num', function () {
    $source = include dirname(__DIR__, 2) . '/models/notfis_proceda_31.php';
    $target = include dirname(__DIR__, 2) . '/models/csv_num2.php';
    $data = dirname(__DIR__, 2) . '/data/notfis_proceda_31.txt';
    $notfisData = Base::convert(
        $source,
        $target,
        $data
    );
    expect(count(Base::getMappedData()))->toBe(11);
    expect(substr_count(trim($notfisData), PHP_EOL))->toBe(11);
})->group('proceda-notfis-to-others');

it('convert proceda notfis to xml', function () {
    $source = include dirname(__DIR__, 2) . '/models/notfis_proceda_31.php';
    $target = include dirname(__DIR__, 2) . '/models/xml.php';
    $data = dirname(__DIR__, 2) . '/data/notfis_proceda_31.txt';
    $notfisData = Base::convert(
        $source,
        $target,
        $data
    );
    $notfisData = json_decode($notfisData, true);
    expect(count(Base::getMappedData()))->toBe(11);
    expect(count($notfisData))->toBe(11);
})->group('proceda-notfis-to-others');

it('convert proceda notfis to json', function () {
    $source = include dirname(__DIR__, 2) . '/models/notfis_proceda_31.php';
    $target = include dirname(__DIR__, 2) . '/models/json.php';
    $data = dirname(__DIR__, 2) . '/data/notfis_proceda_31.txt';
    $notfisData = Base::convert(
        $source,
        $target,
        $data
    );
    $notfisData = json_decode($notfisData, true);
    expect(count(Base::getMappedData()))->toBe(11);
    expect(count($notfisData))->toBe(11);
})->group('proceda-notfis-to-others');

it('convert proceda notfis to collection assoc', function () {
    $source = include dirname(__DIR__, 2) . '/models/notfis_proceda_31.php';
    $target = include dirname(__DIR__, 2) . '/models/collection_assoc.php';
    $data = dirname(__DIR__, 2) . '/data/notfis_proceda_31.txt';
    $collectionData = Base::convert(
        $source,
        $target,
        $data
    );
    $collectionData = json_decode($collectionData, true);
    expect(count(Base::getMappedData()))->toBe(11);
    expect(count($collectionData))->toBe(11);
})->group('proceda-notfis-to-others');

it('convert proceda notfis to collection num', function () {
    $source = include dirname(__DIR__, 2) . '/models/notfis_proceda_31.php';
    $target = include dirname(__DIR__, 2) . '/models/collection_num.php';
    $data = dirname(__DIR__, 2) . '/data/notfis_proceda_31.txt';
    $collectionData = Base::convert(
        $source,
        $target,
        $data
    );
    $collectionData = json_decode($collectionData, true);
    expect(count(Base::getMappedData()))->toBe(11);
    expect(count($collectionData))->toBe(11);
})->group('proceda-notfis-to-others');

it('convert proceda notfis to proceda notfis', function () {
    $source = include dirname(__DIR__, 2) . '/models/notfis_proceda_31.php';
    $target = include dirname(__DIR__, 2) . '/models/notfis_proceda_31.php';
    $data = dirname(__DIR__, 2) . '/data/notfis_proceda_31.txt';
    $collectionData = Base::convert(
        $source,
        $target,
        $data
    );
    $collectionData = json_decode($collectionData, true);
    expect(count(Base::getMappedData()))->toBe(11);
    expect(count($collectionData))->toBe(25);
})->group('proceda-notfis-to-others');
