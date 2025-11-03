<?php

use Inspire\DataMapper\DataType\Decimal;
use Inspire\DataMapper\Mappers\Base;

include dirname(__DIR__, 3) . "/vendor/autoload.php";
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
            // print_r($res);
            $res['EMIT_XNOME2'] = clone $res['EMIT_XNOME'];
            $res['EMIT_XNOME2']->setElement('EMIT_XNOME2');
            $res['DEST_XNOME2'] = clone $res['DEST_XNOME'];
            $res['DEST_XNOME2']->setElement('DEST_XNOME2');
            $res['ID_DOCUMENTO'] = clone $res['DEST_XNOME'];
            $res['ID_DOCUMENTO']->setElement('ID_DOCUMENTO');
            $res['TOTAIS_NF_VL'] = new Decimal([
                'element' => 'TOTAIS_NF_VL',
                'default' => $NF_VL,
                'dec_sep' => '',
                'decimals' => 0
            ]);
            $res['TOTAIS_NF_PESO'] = new Decimal([
                'element' => 'TOTAIS_NF_PESO',
                'default' => $NF_PESO,
                'dec_sep' => '',
                'decimals' => 0
            ]);
        }
        // var_dump($result);
        return $result;
    }
);
$collectionData = json_decode($collectionData, true);
var_dump($collectionData);
