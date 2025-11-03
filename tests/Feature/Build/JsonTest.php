<?php

use Inspire\DataMapper\DataType\Decimal;
use Inspire\DataMapper\Mappers\Base;

it('convert json to csv assoc', function () {
    $source = include dirname(__DIR__, 2) . '/models/json.php';
    $target = include dirname(__DIR__, 2) . '/models/csv_assoc2.php';
    $data = dirname(__DIR__, 2) . '/data/INPUT.json';
    $jsonData = Base::convert(
        $source,
        $target,
        $data
    );
    expect(count(Base::getMappedData()))->toBe(5379);
    expect(substr_count(trim($jsonData), PHP_EOL))->toBe(5379);
})->group('json-to-others');

it('convert json to csv num', function () {
    $source = include dirname(__DIR__, 2) . '/models/json.php';
    $target = include dirname(__DIR__, 2) . '/models/csv_num2.php';
    $data = dirname(__DIR__, 2) . '/data/INPUT.json';
    $jsonData = Base::convert(
        $source,
        $target,
        $data
    );
    expect(count(Base::getMappedData()))->toBe(5379);
    expect(substr_count(trim($jsonData), PHP_EOL))->toBe(5379);
})->group('json-to-others');

it('convert json to xml', function () {
    $source = include dirname(__DIR__, 2) . '/models/json.php';
    $target = include dirname(__DIR__, 2) . '/models/xml.php';
    $data = dirname(__DIR__, 2) . '/data/INPUT.json';
    $jsonData = Base::convert(
        $source,
        $target,
        $data
    );
    $jsonData = json_decode($jsonData);
    expect(count(Base::getMappedData()))->toBe(5379);
    expect(count($jsonData))->toBe(5379);
})->group('json-to-others');

it('convert json to json', function () {
    $source = include dirname(__DIR__, 2) . '/models/json.php';
    $target = include dirname(__DIR__, 2) . '/models/json.php';
    $data = dirname(__DIR__, 2) . '/data/INPUT.json';
    $jsonData = Base::convert(
        $source,
        $target,
        $data
    );
    $jsonData = json_decode($jsonData);
    expect(count(Base::getMappedData()))->toBe(5379);
    expect(count($jsonData))->toBe(5379);
})->group('json-to-others');

it('convert json to collection assoc', function () {
    $source = include dirname(__DIR__, 2) . '/models/json.php';
    $target = include dirname(__DIR__, 2) . '/models/collection_assoc.php';
    $data = dirname(__DIR__, 2) . '/data/INPUT.json';
    $collectionData = Base::convert(
        $source,
        $target,
        $data
    );
    $collectionData = json_decode($collectionData, true);
    expect(count(Base::getMappedData()))->toBe(5379);
    expect(count($collectionData))->toBe(5379);
})->group('json-to-others');

it('convert json to collection num', function () {
    $source = include dirname(__DIR__, 2) . '/models/json.php';
    $target = include dirname(__DIR__, 2) . '/models/collection_num.php';
    $data = dirname(__DIR__, 2) . '/data/INPUT.json';
    $collectionData = Base::convert(
        $source,
        $target,
        $data
    );
    $collectionData = json_decode($collectionData, true);
    expect(count(Base::getMappedData()))->toBe(5379);
    expect(count($collectionData))->toBe(5379);
})->group('json-to-others');

it('convert json to proceda notfis', function () {
    $source = include dirname(__DIR__, 2) . '/models/json.php';
    $target = include dirname(__DIR__, 2) . '/models/notfis_proceda_31.php';
    $data = dirname(__DIR__, 2) . '/data/INPUT.json';
    $collectionData = Base::convert(
        $source,
        $target,
        $data,
        function ($result) {
            $NF_PESO = array_sum(
                array_map(function ($item) {
                    return floatval($item['NF_PESO']->getValue()->getValue());
                }, $result)
            );
            $NF_VL = array_sum(
                array_map(function ($item) {
                    return floatval($item['NF_VL']->getValue()->getValue());
                }, $result)
            );
            foreach ($result as &$res) {
                $res['EMIT_XNOME2'] = clone $result[0]['EMIT_XNOME'];
                $res['EMIT_XNOME2']->setElement('EMIT_XNOME2');
                $res['DEST_XNOME2'] = clone $result[0]['DEST_XNOME'];
                $res['DEST_XNOME2']->setElement('DEST_XNOME2');
                $res['ID_DOCUMENTO'] = clone $result[0]['DEST_XNOME'];
                $res['ID_DOCUMENTO']->setElement('ID_DOCUMENTO');
            }
            $result[count($result) - 1]['TOTAIS_NF_VL'] = new Decimal([
                'element' => 'TOTAIS_NF_VL',
                'default' => $NF_VL,
                'dec_sep' => '',
                'decimals' => 0
            ]);
            $result[count($result) - 1]['TOTAIS_NF_PESO'] = new Decimal([
                'element' => 'TOTAIS_NF_PESO',
                'default' => $NF_PESO,
                'dec_sep' => '',
                'decimals' => 0
            ]);
            return $result;
        }
    );
    $collectionData = json_decode($collectionData, true);
    expect(count(Base::getMappedData()))->toBe(5379);
    expect(count($collectionData))->toBe(16140);
})->group('json-to-others');
