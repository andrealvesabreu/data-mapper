<?php

return [
    'parser' => 'proceda',
    'setup' => [
        'fillMissing' => true, //Auto fill elements missing that have a default value in mnodel
        'checkInput' => false, //Check data while read from source
        'checkOutput' => false, //Check data while writing to destination
        'filler' => [
            'datetime' => '0',
            'float' => '0',
            'integer' => '0',
            'enum' => ' ',
            'numeric' => '0',
            'string' => ' '
        ],
        'align' => [
            'datetime' => 'left',
            'float' => 'right',
            'integer' => 'right',
            'enum' => 'left',
            'numeric' => 'left',
            'string' => 'left'
        ],
        'length' => 240,
        'pivot' => 'NF_NUMERO'
    ],
    'definitions' =>
    [
        'R000' => [
            'find' => function ($ln) {
                return substr($ln, 0, 3) == '000';
            },
            'minItems' => 1,
            'maxItems' => 1,
            'key' => [
                'EMIT_XNOME2',
                'DEST_XNOME2'
            ],
            'elements' =>
            [
                'rem_xNome2' => [
                    'element' => 'EMIT_XNOME2',
                    'type' => 'string',
                    'required' => true,
                    'start' => 4,
                    'minLength' => 35,
                    'maxLength' => 35
                ],
                'dest_xFant2' => [
                    'element' => 'DEST_XNOME2',
                    'required' => true,
                    'type' => 'string',
                    'start' => 39,
                    'minLength' => 35,
                    'maxLength' => 35
                ],
                'data' => [
                    'element' => 'DATA_EMISSAO',
                    'type' => 'datetime',
                    'required' => true,
                    'start' => 74,
                    'minLength' => 6,
                    'maxLength' => 6,
                    'format' => 'ddmmyy'
                ],
                'hora' => [
                    'element' => 'HORA_EMISSAO',
                    'type' => 'datetime',
                    'start' => 80,
                    'minLength' => 4,
                    'maxLength' => 4,
                    'format' => 'hhii'
                ],
                'idInterchange' => [
                    'element' => 'ID_INTERCHANGE',
                    'type' => 'string',
                    'start' => 84,
                    'minLength' => 12,
                    'maxLength' => 12
                ],
                'filler' => [
                    'element' => 'FILLER',
                    'type' => 'string',
                    'start' => 96,
                    'minLength' => 145,
                    'maxLength' => 145
                ]
            ]
        ],
        'R310' => [
            'find' => function ($ln) {
                return substr($ln, 0, 3) == '310';
            },
            'minItems' => 1,
            'maxItems' => 200,
            'parent' => 'R000',
            'key' => ['ID_DOCUMENTO'],
            'elements' =>
            [
                'idInterchangeDoc' => [
                    'element' => 'ID_DOCUMENTO',
                    'type' => 'string',
                    'start' => 4,
                    'minLength' => 14,
                    'maxLength' => 14
                ],
                'filler' => [
                    'element' => 'FILLER',
                    'type' => 'string',
                    'start' => 18,
                    'minLength' => 223,
                    'maxLength' => 223
                ]
            ]
        ],
        'R311' => [
            'minItems' => 1,
            'maxItems' => 10,
            'parent' => 'R310',
            'key' => [
                'EMIT_CNP'
            ],
            'find' => function ($ln) {
                return substr($ln, 0, 3) == '311';
            },
            'elements' =>
            [
                'rem_CNPJ' => [
                    'element' => 'EMIT_CNP',
                    'type' => 'numeric',
                    'required' => true,
                    'start' => 4,
                    'minLength' => 14,
                    'maxLength' => 14
                ],
                'rem_IE' => [
                    'element' => 'EMIT_IE',
                    'type' => 'string',
                    'required' => false,
                    'start' => 18,
                    'minLength' => 15,
                    'maxLength' => 15
                ],
                'rem_xLgr' => [
                    'element' => 'EMIT_XLGR',
                    'type' => 'string',
                    'required' => false,
                    'start' => 33,
                    'minLength' => 40,
                    'maxLength' => 40
                ],
                'rem_xMun' => [
                    'element' => 'EMIT_XMUN',
                    'type' => 'string',
                    'required' => false,
                    'start' => 73,
                    'minLength' => 35,
                    'maxLength' => 35
                ],
                'rem_CEP' => [
                    'element' => 'EMIT_CEP',
                    'type' => 'numeric',
                    'start' => 108,
                    'minLength' => 9,
                    'maxLength' => 9
                ],
                'rem_xUF' => [
                    'element' => 'EMIT_XUF',
                    'type' => 'string',
                    'start' => 117,
                    'minLength' => 9,
                    'maxLength' => 9
                ],
                'data_embarque' => [
                    'element' => 'DATA_EMBARQUE',
                    'type' => 'datetime',
                    'format' => 'ddmmyyyy',
                    'start' => 126,
                    'minLength' => 8,
                    'maxLength' => 8
                ],
                'rem_xNome' => [
                    'element' => 'EMIT_XNOME',
                    'type' => 'string',
                    'start' => 134,
                    'minLength' => 40,
                    'maxLength' => 40
                ],
                'filler' => [
                    'element' => 'FILLER',
                    'type' => 'string',
                    'start' => 174,
                    'minLength' => 67,
                    'maxLength' => 67
                ],
            ]
        ],
        'R312' => [
            'minItems' => 1,
            'maxItems' => 500,
            'parent' => 'R311',
            'key' => [
                // 'DEST_CNP'
            ],
            'find' => function ($ln) {
                return substr($ln, 0, 3) == '312';
            },
            'elements' =>
            [
                'dest_xNome' => [
                    'element' => 'DEST_XNOME',
                    'type' => 'string',
                    'required' => false,
                    'start' => 4,
                    'minLength' => 40,
                    'maxLength' => 40
                ],
                'dest_CNPJ' => [
                    'element' => 'DEST_CNP',
                    'type' => 'numeric',
                    'required' => false,
                    'start' => 44,
                    'minLength' => 14,
                    'maxLength' => 14
                ],
                'dest_IE' => [
                    'element' => 'DEST_IE',
                    'type' => 'numeric',
                    'start' => 58,
                    'minLength' => 15,
                    'maxLength' => 15
                ],
                'dest_xLgr' => [
                    'element' => 'DEST_XLGR',
                    'type' => 'string',
                    'required' => false,
                    'start' => 73,
                    'minLength' => 40,
                    'maxLength' => 40
                ],
                'dest_xBairro' => [
                    'element' => 'DEST_XBAIRRO',
                    'type' => 'string',
                    'required' => false,
                    'start' => 113,
                    'minLength' => 20,
                    'maxLength' => 20
                ],
                'dest_xMun' => [
                    'element' => 'DEST_XMUN',
                    'type' => 'string',
                    'required' => false,
                    'start' => 133,
                    'minLength' => 35,
                    'maxLength' => 35
                ],
                'dest_CEP' => [
                    'element' => 'DEST_CEP',
                    'type' => 'numeric',
                    'start' => 168,
                    'minLength' => 9,
                    'maxLength' => 9
                ],
                'dest_cMun' => [
                    'element' => 'DEST_CMUN',
                    'type' => 'numeric',
                    'start' => 177,
                    'minLength' => 9,
                    'maxLength' => 9
                ],
                'dest_UF' => [
                    'element' => 'DEST_XUF',
                    'type' => 'string',
                    'start' => 186,
                    'minLength' => 9,
                    'maxLength' => 9
                ],
                'area_frete' => [
                    'element' => 'AREA_FRETE',
                    'type' => 'string',
                    'start' => 195,
                    'minLength' => 4,
                    'maxLength' => 4
                ],
                'dest_telefone' => [
                    'element' => 'DEST_FONE',
                    'type' => 'string',
                    'start' => 199,
                    'minLength' => 35,
                    'maxLength' => 35
                ],
                'tpdoc_dest' => [
                    'element' => 'DEST_TP_CAD',
                    'type' => 'string',
                    'start' => 234,
                    'minLength' => 1,
                    'maxLength' => 1
                ],
                'filler' => [
                    'element' => 'FILLER',
                    'type' => 'string',
                    'start' => 235,
                    'minLength' => 6,
                    'maxLength' => 6
                ],
            ]
        ],
        'R313' => [
            'minItems' => 1,
            'maxItems' => 40,
            'parent' => 'R312',
            'key' => [],
            'find' => function ($ln) {
                return substr($ln, 0, 3) == '313';
            },
            'elements' =>
            [
                'romaneio' => [
                    'element' => 'ROMANEIO',
                    'type' => 'string',
                    'start' => 4,
                    'minLength' => 15,
                    'maxLength' => 15
                ],
                'cod_rota' => [
                    'element' => 'COD_ROTA',
                    'type' => 'string',
                    'start' => 19,
                    'minLength' => 7,
                    'maxLength' => 7
                ],
                'meio_transp' => [
                    'element' => 'MODAL',
                    'type' => 'string',
                    'start' => 26,
                    'minLength' => 1,
                    'maxLength' => 1
                ],
                'tp_transp' => [
                    'element' => 'TP_TRANSPORTE',
                    'type' => 'enum',
                    'start' => 27,
                    'minLength' => 1,
                    'maxLength' => 1,
                    'enum' => ['1', '2']
                ],
                'tp_carga' => [
                    'element' => 'TP_CARGA',
                    'type' => 'enum',
                    'default' => '3',
                    'start' => 28,
                    'minLength' => 1,
                    'maxLength' => 1,
                    'enum' => ['1', '2', '3']
                ],
                'cond_frete' => [
                    'element' => 'TP_COB_FRETE',
                    'type' => 'enum',
                    'start' => 29,
                    'minLength' => 1,
                    'maxLength' => 1,
                    'enum' => ['C', 'F']
                ],
                'infNF_serie' => [
                    'element' => 'NF_SERIE',
                    'required' => true,
                    'type' => 'string',
                    'start' => 30,
                    'minLength' => 3,
                    'maxLength' => 3
                ],
                'infNF_numero' => [
                    'element' => 'NF_NUMERO',
                    'required' => true,
                    'type' => 'integer',
                    'start' => 33,
                    'minLength' => 8,
                    'maxLength' => 8
                ],
                'infNF_data' => [
                    'element' => 'NF_DATA',
                    'type' => 'datetime',
                    'format' => 'ddmmyyyy',
                    'start' => 41,
                    'minLength' => 8,
                    'maxLength' => 8
                ],
                'infNF_xNat' => [
                    'element' => 'NF_NATUREZA',
                    'type' => 'string',
                    'start' => 49,
                    'minLength' => 15,
                    'maxLength' => 15
                ],
                'infNF_xEsp' => [
                    'element' => 'NF_ESPECIE',
                    'type' => 'string',
                    'start' => 64,
                    'minLength' => 15,
                    'maxLength' => 15
                ],
                'infNF_qVol' => [
                    'element' => 'NF_VOLUMES',
                    'type' => 'decimal',
                    'start' => 79,
                    'minLength' => 7,
                    'maxLength' => 7,
                    'decimals' => 2,
                    'minimum' => 0,
                    'maximum' => 9999999
                ],
                'infNF_vNF' => [
                    'element' => 'NF_VL',
                    'type' => 'decimal',
                    'start' => 86,
                    'minLength' => 15,
                    'maxLength' => 15,
                    'minimum' => 0,
                    'maximum' => 99999999999999,
                    'decimals' => 2
                ],
                'infNF_qPeso' => [
                    'element' => 'NF_PESO',
                    'type' => 'decimal',
                    'start' => 101,
                    'minLength' => 7,
                    'maxLength' => 7,
                    'minimum' => 0,
                    'maximum' => 9999999,
                    'decimals' => 2
                ],
                'infNF_qPesoCub' => [
                    'element' => 'NF_PESO_CUBADO',
                    'type' => 'decimal',
                    'start' => 108,
                    'minLength' => 5,
                    'maxLength' => 5,
                    'minimum' => 0,
                    'maximum' => 9999999,
                    'decimals' => 2
                ],
                'tp_icms' => [
                    'element' => 'NF_TP_ICMS',
                    'type' => 'enum',
                    'start' => 113,
                    'minLength' => 1,
                    'maxLength' => 1,
                    'enum' => ['D', 'R', 'P', 'T', 'S', 'N']
                ],
                'segurado' => [
                    'element' => 'NF_IND_SEG',
                    'type' => 'enum',
                    'start' => 114,
                    'minLength' => 1,
                    'maxLength' => 1,
                    'enum' => 'enums.SIM_NAO_BOOL'
                ],
                'seguro_valor' => [
                    'element' => 'NF_SEG_VL',
                    'type' => 'decimal',
                    'start' => 115,
                    'minLength' => 15,
                    'maxLength' => 15,
                    'minimum' => 0,
                    'maximum' => 99999999999999,
                    'decimals' => 2
                ],
                'valor_receber' => [
                    'element' => 'FRETE_TOTAL_VL',
                    'type' => 'decimal',
                    'start' => 130,
                    'minLength' => 15,
                    'maxLength' => 15,
                    'minimum' => 0,
                    'maximum' => 99999999999999,
                    'decimals' => 2
                ],
                'placa' => [
                    'element' => 'VEICULO_PLACA',
                    'type' => 'string',
                    'start' => 145,
                    'minLength' => 7,
                    'maxLength' => 7
                ],
                'plano_carga' => [
                    'element' => 'NF_IND_PCR',
                    'type' => 'enum',
                    'start' => 152,
                    'minLength' => 1,
                    'maxLength' => 1,
                    'enum' => 'enums.SIM_NAO_BOOL'
                ],
                'valor_taxa_peso' => [
                    'element' => 'FRETE_PESO_VL',
                    'type' => 'decimal',
                    'start' => 153,
                    'minLength' => 15,
                    'maxLength' => 15,
                    'minimum' => 0,
                    'maximum' => 99999999999999,
                    'decimals' => 2
                ],
                'valor_adv' => [
                    'element' => 'FRETE_ADV_VL',
                    'type' => 'decimal',
                    'start' => 168,
                    'minLength' => 15,
                    'maxLength' => 15,
                    'minimum' => 0,
                    'maximum' => 99999999999999,
                    'decimals' => 2
                ],
                'valor_taxas' => [
                    'element' => 'NF_TX_VL',
                    'type' => 'decimal',
                    'start' => 183,
                    'minLength' => 15,
                    'maxLength' => 15,
                    'minimum' => 0,
                    'maximum' => 99999999999999,
                    'decimals' => 2
                ],
                'valor_prestacao' => [
                    'element' => 'FRETE_VL',
                    'type' => 'decimal',
                    'start' => 198,
                    'minLength' => 15,
                    'maxLength' => 15,
                    'minimum' => 0,
                    'maximum' => 99999999999999,
                    'decimals' => 2
                ],
                'acao' => [
                    'element' => 'NF_IND_ACAO',
                    'type' => 'enum',
                    'start' => 213,
                    'minLength' => 1,
                    'maxLength' => 1,
                    'enum' => ['I', 'E']
                ],
                'valor_icms' => [
                    'element' => 'NF_ICMS_VL',
                    'type' => 'decimal',
                    'start' => 214,
                    'minLength' => 12,
                    'maxLength' => 12,
                    'minimum' => 0,
                    'maximum' => 99999999999999,
                    'decimals' => 2
                ],
                'valor_icms_ret' => [
                    'element' => 'NF_ICMS_RET_VL',
                    'type' => 'decimal',
                    'start' => 226,
                    'minLength' => 12,
                    'maxLength' => 12,
                    'minimum' => 0,
                    'maximum' => 99999999999999,
                    'decimals' => 2
                ],
                'bomificacao' => [
                    'element' => 'NF_IND_BONIFICACAO',
                    'type' => 'enum',
                    'start' => 238,
                    'minLength' => 1,
                    'maxLength' => 1,
                    'enum' => 'enums.SIM_NAO_BOOL'
                ],
                'nf_chave' => [
                    'element' => 'NF_CHAVE',
                    'type' => 'numeric',
                    'start' => 239,
                    'minLength' => 44,
                    'maxLength' => 44
                ]
            ]
        ],
        'R333' => [
            'minItems' => 1,
            'maxItems' => 1,
            'parent' => 'R313',
            'key' => [],
            'find' => function ($ln) {
                return substr($ln, 0, 3) == '333';
            },
            'elements' =>
            [
                'nf_cfop' => [
                    'element' => 'NF_CFOP',
                    'required' => true,
                    'type' => 'numeric',
                    'start' => 4,
                    'minLength' => 4,
                    'maxLength' => 4
                ],
                'tp_periodo_entrega' => [
                    'element' => 'TP_PER_ENT',
                    'required' => true,
                    'type' => 'enum',
                    'start' => 8,
                    'minLength' => 1,
                    'maxLength' => 1,
                    'enum' => ['0', '1', '2', '3', '4']
                ],
                'data_inicial_entrega' => [
                    'element' => 'DT_INI_ENT',
                    'required' => true,
                    'type' => 'datetime',
                    'format' => 'ddmmyyyy',
                    'start' => 9,
                    'minLength' => 8,
                    'maxLength' => 8
                ],
                'hora_inicial_entrega' => [
                    'element' => 'HR_INI_ENT',
                    'required' => true,
                    'type' => 'datetime',
                    'format' => 'hhmm',
                    'start' => 17,
                    'minLength' => 4,
                    'maxLength' => 4
                ],
                'data_final_entrega' => [
                    'element' => 'DT_FIM_ENT',
                    'type' => 'datetime',
                    'format' => 'ddmmyyyy',
                    'start' => 21,
                    'minLength' => 8,
                    'maxLength' => 8
                ],
                'hora_final_entrega' => [
                    'element' => 'HR_FIM_ENT',
                    'type' => 'datetime',
                    'format' => 'hhmm',
                    'start' => 29,
                    'minLength' => 4,
                    'maxLength' => 4
                ],
                'local_desembarque' => [
                    'element' => 'DOCA',
                    'type' => 'string',
                    'start' => 33,
                    'minLength' => 15,
                    'maxLength' => 15
                ],
                'calc_frete_dif' => [
                    'element' => 'IND_FRETE_DIF',
                    'type' => 'enum',
                    'start' => 48,
                    'minLength' => 1,
                    'maxLength' => 1,
                    'enum' => 'enums.SIM_NAO_BOOL'
                ],
                'tabela_preco' => [
                    'element' => 'TBL_FRETE',
                    'type' => 'string',
                    'start' => 49,
                    'minLength' => 10,
                    'maxLength' => 10
                ],
                'infNF_emit1' => [
                    'element' => 'NF_EMIT_CNPJ',
                    'type' => 'numeric',
                    'start' => 59,
                    'minLength' => 15,
                    'maxLength' => 15
                ],
                'infNF_serie1' => [
                    'element' => 'NF_SERIE',
                    'type' => 'string',
                    'start' => 74,
                    'minLength' => 3,
                    'maxLength' => 3
                ],
                'infNF_numero1' => [
                    'element' => 'NF_NUMERO',
                    'type' => 'integer',
                    'start' => 77,
                    'minLength' => 8,
                    'maxLength' => 8
                ],
                'infNF_emit2' => [
                    'element' => 'NF_EMIT_CNPJ',
                    'type' => 'numeric',
                    'start' => 85,
                    'minLength' => 15,
                    'maxLength' => 15
                ],
                'infNF_serie2' => [
                    'element' => 'NF_SERIE',
                    'type' => 'string',
                    'start' => 100,
                    'minLength' => 3,
                    'maxLength' => 3
                ],
                'infNF_numero2' => [
                    'element' => 'NF_NUMERO',
                    'type' => 'integer',
                    'start' => 103,
                    'minLength' => 8,
                    'maxLength' => 8
                ],
                'infNF_emit3' => [
                    'element' => 'NF_EMIT_CNPJ',
                    'type' => 'numeric',
                    'start' => 111,
                    'minLength' => 15,
                    'maxLength' => 15
                ],
                'infNF_serie3' => [
                    'element' => 'NF_SERIE',
                    'type' => 'string',
                    'start' => 126,
                    'minLength' => 3,
                    'maxLength' => 3
                ],
                'infNF_numero3' => [
                    'element' => 'NF_NUMERO',
                    'type' => 'integer',
                    'start' => 129,
                    'minLength' => 8,
                    'maxLength' => 8
                ],
                'infNF_emit4' => [
                    'element' => 'NF_EMIT_CNPJ',
                    'type' => 'numeric',
                    'start' => 137,
                    'minLength' => 15,
                    'maxLength' => 15
                ],
                'infNF_serie4' => [
                    'element' => 'NF_SERIE',
                    'type' => 'string',
                    'start' => 152,
                    'minLength' => 3,
                    'maxLength' => 3
                ],
                'infNF_numero4' => [
                    'element' => 'NF_NUMERO',
                    'type' => 'integer',
                    'start' => 155,
                    'minLength' => 8,
                    'maxLength' => 8
                ],
                'infNF_emit5' => [
                    'element' => 'NF_EMIT_CNPJ',
                    'type' => 'numeric',
                    'start' => 163,
                    'minLength' => 15,
                    'maxLength' => 15
                ],
                'infNF_serie5' => [
                    'element' => 'NF_SERIE',
                    'type' => 'string',
                    'start' => 178,
                    'minLength' => 3,
                    'maxLength' => 3
                ],
                'infNF_numero5' => [
                    'element' => 'NF_NUMERO',
                    'type' => 'integer',
                    'start' => 181,
                    'minLength' => 8,
                    'maxLength' => 8
                ],
                'valor_desp_adic' => [
                    'element' => 'FRETE_ADIC_VL',
                    'type' => 'decimal',
                    'start' => 189,
                    'minLength' => 12,
                    'maxLength' => 12,
                    'minimum' => 0,
                    'maximum' => 99999999999999,
                    'decimals' => 2
                ],
                'tp_veic_transp' => [
                    'element' => 'TP_VEICULO',
                    'type' => 'enum',
                    'start' => 204,
                    'minLength' => 5,
                    'maxLength' => 5,
                    'enum' => ['12', '21', '23', '25', '31', '32', '34', '41', '43', '51', '52', '55', '101', 'BR01', 'BR02', 'BR03', 'BR04', 'BR05', 'BR06', 'BR07', 'BR08', 'BR10', 'BR11', 'BR12', 'BR13', 'BR60', 'BR80', 'C20', 'C40', 'C4H', 'C2R', 'C4']
                ],
                'filial_cte_redespacho' => [
                    'element' => 'REDESP_BASE_CTE',
                    'type' => 'string',
                    'start' => 209,
                    'minLength' => 10,
                    'maxLength' => 10
                ],
                'serie_cte_redespacho' => [
                    'element' => 'REDESP_CTE_SERIE',
                    'type' => 'string',
                    'start' => 219,
                    'minLength' => 5,
                    'maxLength' => 5
                ],
                'numero_cte_redespacho' => [
                    'element' => 'REDESP_CTE_NUMERO',
                    'type' => 'string',
                    'start' => 224,
                    'minLength' => 12,
                    'maxLength' => 12
                ],
                'filler' => [
                    'element' => 'FILLER',
                    'type' => 'string',
                    'start' => 236,
                    'minLength' => 5,
                    'maxLength' => 5
                ]
            ]
        ],
        'R314' => [
            'minItems' => 1,
            'maxItems' => 5,
            'parent' => 'R313',
            'key' => [],
            'find' => function ($ln) {
                return substr($ln, 0, 3) == '314';
            },
            'elements' => [
                'infNF_qVol1' => [
                    'element' => 'VOLUME_QUANT1',
                    'type' => 'numeric',
                    'required' => true,
                    'start' => 4,
                    'minLength' => 7,
                    'maxLength' => 7,
                    'decimals' => 2,
                    'minimum' => 0,
                    'maximum' => 9999999
                ],
                'infNF_xEsp1' => [
                    'element' => 'VOLUME_ESPECIE1',
                    'type' => 'string',
                    'start' => 11,
                    'minLength' => 15,
                    'maxLength' => 15
                ],
                'infNF_prod1' => [
                    'element' => 'VOLUME_DESC1',
                    'type' => 'string',
                    'start' => 26,
                    'minLength' => 30,
                    'maxLength' => 30
                ],
                'infNF_qVol2' => [
                    'element' => 'VOLUME_QUANT2',
                    'type' => 'numeric',
                    'required' => true,
                    'start' => 56,
                    'minLength' => 7,
                    'maxLength' => 7,
                    'decimals' => 2,
                    'minimum' => 0,
                    'maximum' => 9999999
                ],
                'infNF_xEsp2' => [
                    'element' => 'VOLUME_ESPECIE2',
                    'type' => 'string',
                    'start' => 63,
                    'minLength' => 15,
                    'maxLength' => 15
                ],
                'infNF_prod2' => [
                    'element' => 'VOLUME_DESC2',
                    'type' => 'string',
                    'start' => 88,
                    'minLength' => 30,
                    'maxLength' => 30
                ],
                'infNF_qVol3' => [
                    'element' => 'VOLUME_QUANT3',
                    'type' => 'numeric',
                    'required' => true,
                    'start' => 108,
                    'minLength' => 7,
                    'maxLength' => 7,
                    'decimals' => 2,
                    'minimum' => 0,
                    'maximum' => 9999999
                ],
                'infNF_xEsp3' => [
                    'element' => 'VOLUME_ESPECIE3',
                    'type' => 'string',
                    'start' => 115,
                    'minLength' => 15,
                    'maxLength' => 15
                ],
                'infNF_prod3' => [
                    'element' => 'VOLUME_DESC3',
                    'type' => 'string',
                    'start' => 130,
                    'minLength' => 30,
                    'maxLength' => 30
                ],
                'infNF_qVol4' => [
                    'element' => 'VOLUME_QUANT4',
                    'type' => 'numeric',
                    'required' => true,
                    'start' => 160,
                    'minLength' => 7,
                    'maxLength' => 7,
                    'decimals' => 2,
                    'minimum' => 0,
                    'maximum' => 9999999
                ],
                'infNF_xEsp4' => [
                    'element' => 'VOLUME_ESPECIE4',
                    'type' => 'string',
                    'start' => 167,
                    'minLength' => 15,
                    'maxLength' => 15
                ],
                'infNF_prod4' => [
                    'element' => 'VOLUME_DESC4',
                    'type' => 'string',
                    'start' => 182,
                    'minLength' => 30,
                    'maxLength' => 30
                ],
                'filler' => [
                    'element' => 'FILLER',
                    'type' => 'string',
                    'start' => 212,
                    'minLength' => 5,
                    'maxLength' => 5
                ]
            ]
        ],
        'R315' => [
            'minItems' => 1,
            'maxItems' => 1,
            'parent' => 'R313',
            'key' => [
                'RECEB_CNP'
            ],
            'find' => function ($ln) {
                return substr($ln, 0, 3) == '315';
            },
            'elements' =>
            [
                'receb_xNome' => [
                    'element' => 'RECEB_XNOME',
                    'type' => 'string',
                    'required' => true,
                    'start' => 4,
                    'minLength' => 40,
                    'maxLength' => 40
                ],
                'receb_CNPJ' => [
                    'element' => 'RECEB_CNP',
                    'type' => 'numeric',
                    'required' => true,
                    'start' => 44,
                    'minLength' => 14,
                    'maxLength' => 14
                ],
                'receb_IE' => [
                    'element' => 'RECEB_IE',
                    'type' => 'numeric',
                    'start' => 58,
                    'minLength' => 15,
                    'maxLength' => 15
                ],
                'receb_xLgr' => [
                    'element' => 'RECEB_XLGR',
                    'type' => 'string',
                    'required' => true,
                    'start' => 73,
                    'minLength' => 40,
                    'maxLength' => 40
                ],
                'receb_xBairro' => [
                    'element' => 'RECEB_XBAIRRO',
                    'type' => 'string',
                    'required' => true,
                    'start' => 113,
                    'minLength' => 20,
                    'maxLength' => 20
                ],
                'receb_xMun' => [
                    'element' => 'RECEB_XMUN',
                    'type' => 'string',
                    'required' => true,
                    'start' => 133,
                    'minLength' => 35,
                    'maxLength' => 35
                ],
                'receb_CEP' => [
                    'element' => 'RECEB_CEP',
                    'type' => 'numeric',
                    'start' => 168,
                    'minLength' => 9,
                    'maxLength' => 9
                ],
                'receb_cMun' => [
                    'element' => 'RECEB_CMUN',
                    'type' => 'numeric',
                    'start' => 177,
                    'minLength' => 9,
                    'maxLength' => 9
                ],
                'receb_UF' => [
                    'element' => 'RECEB_XUF',
                    'type' => 'string',
                    'start' => 186,
                    'minLength' => 9,
                    'maxLength' => 9
                ],
                'receb_telefone' => [
                    'element' => 'RECEB_FONE',
                    'type' => 'string',
                    'start' => 195,
                    'minLength' => 35,
                    'maxLength' => 35
                ],
                'filler' => [
                    'element' => 'FILLER',
                    'type' => 'string',
                    'start' => 230,
                    'minLength' => 11,
                    'maxLength' => 11
                ],
            ]
        ],
        'R316' => [
            'minItems' => 1,
            'maxItems' => 1,
            'parent' => 'R313',
            'key' => [
                'EXPED_CNP'
            ],
            'find' => function ($ln) {
                return substr($ln, 0, 3) == '316';
            },
            'elements' =>
            [
                'exped_xNome' => [
                    'element' => 'EXPED_XNOME',
                    'type' => 'string',
                    'required' => true,
                    'start' => 4,
                    'minLength' => 40,
                    'maxLength' => 40
                ],
                'exped_CNPJ' => [
                    'element' => 'EXPED_CNP',
                    'type' => 'numeric',
                    'required' => true,
                    'start' => 44,
                    'minLength' => 14,
                    'maxLength' => 14
                ],
                'exped_IE' => [
                    'element' => 'EXPED_IE',
                    'type' => 'numeric',
                    'start' => 58,
                    'minLength' => 15,
                    'maxLength' => 15
                ],
                'exped_xLgr' => [
                    'element' => 'EXPED_XLGR',
                    'type' => 'string',
                    'required' => true,
                    'start' => 73,
                    'minLength' => 40,
                    'maxLength' => 40
                ],
                'exped_xBairro' => [
                    'element' => 'EXPED_XBAIRRO',
                    'type' => 'string',
                    'required' => true,
                    'start' => 113,
                    'minLength' => 20,
                    'maxLength' => 20
                ],
                'exped_xMun' => [
                    'element' => 'EXPED_XMUN',
                    'type' => 'string',
                    'required' => true,
                    'start' => 133,
                    'minLength' => 35,
                    'maxLength' => 35
                ],
                'exped_CEP' => [
                    'element' => 'EXPED_CEP',
                    'type' => 'numeric',
                    'start' => 168,
                    'minLength' => 9,
                    'maxLength' => 9
                ],
                'exped_cMun' => [
                    'element' => 'EXPED_CMUN',
                    'type' => 'numeric',
                    'start' => 177,
                    'minLength' => 9,
                    'maxLength' => 9
                ],
                'exped_UF' => [
                    'element' => 'EXPED_XUF',
                    'type' => 'string',
                    'start' => 186,
                    'minLength' => 9,
                    'maxLength' => 9
                ],
                'area_frete' => [
                    'element' => 'EXPED_AREA_FRETE',
                    'type' => 'string',
                    'start' => 195,
                    'minLength' => 4,
                    'maxLength' => 4
                ],
                'exped_telefone' => [
                    'element' => 'EXPED_FONE',
                    'type' => 'string',
                    'start' => 199,
                    'minLength' => 35,
                    'maxLength' => 35
                ],
                'filler' => [
                    'element' => 'FILLER',
                    'type' => 'string',
                    'start' => 234,
                    'minLength' => 7,
                    'maxLength' => 7
                ],
            ]
        ],
        'R317' => [
            'minItems' => 1,
            'maxItems' => 1,
            'parent' => 'R313',
            'key' => [
                'TOMA_CNP'
            ],
            'find' => function ($ln) {
                return substr($ln, 0, 3) == '317';
            },
            'elements' =>
            [
                'resp_frete_xNome' => [
                    'element' => 'TOMA_XNOME',
                    'required' => true,
                    'type' => 'string',
                    'start' => 4,
                    'minLength' => 40,
                    'maxLength' => 40
                ],
                'resp_frete_CNPJ' => [
                    'element' => 'TOMA_CNP',
                    'required' => true,
                    'type' => 'numeric',
                    'start' => 44,
                    'minLength' => 14,
                    'maxLength' => 14
                ],
                'resp_frete_IE' => [
                    'element' => 'TOMA_IE',
                    'type' => 'numeric',
                    'start' => 58,
                    'minLength' => 15,
                    'maxLength' => 15
                ],
                'resp_frete_xLgr' => [
                    'element' => 'TOMA_XLGR',
                    'required' => true,
                    'type' => 'string',
                    'start' => 73,
                    'minLength' => 40,
                    'maxLength' => 40
                ],
                'resp_frete_xBairro' => [
                    'element' => 'TOMA_XBAIRRO',
                    'required' => true,
                    'type' => 'string',
                    'start' => 113,
                    'minLength' => 20,
                    'maxLength' => 20
                ],
                'resp_frete_xMun' => [
                    'element' => 'TOMA_XMUN',
                    'required' => true,
                    'type' => 'string',
                    'start' => 133,
                    'minLength' => 35,
                    'maxLength' => 35
                ],
                'resp_frete_CEP' => [
                    'element' => 'TOMA_CEP',
                    'type' => 'numeric',
                    'start' => 168,
                    'minLength' => 9,
                    'maxLength' => 9
                ],
                'resp_frete_cMun' => [
                    'element' => 'TOMA_CMUN',
                    'type' => 'numeric',
                    'start' => 177,
                    'minLength' => 9,
                    'maxLength' => 9
                ],
                'resp_frete_UF' => [
                    'element' => 'TOMA_XUF',
                    'type' => 'string',
                    'start' => 186,
                    'minLength' => 9,
                    'maxLength' => 9
                ],
                'resp_frete_telefone' => [
                    'element' => 'TOMA_FONE',
                    'type' => 'string',
                    'start' => 195,
                    'minLength' => 35,
                    'maxLength' => 35
                ],
                'filler' => [
                    'element' => 'FILLER',
                    'type' => 'string',
                    'start' => 230,
                    'minLength' => 11,
                    'maxLength' => 11
                ],
            ]
        ],
        'R318' => [
            'minItems' => 1,
            'maxItems' => 1,
            'parent' => 'R310',
            'key' => [],
            'find' => function ($ln) {
                return substr($ln, 0, 3) == '318';
            },
            'elements' =>
            [
                'valor_total_nf' => [
                    'element' => 'TOTAIS_NF_VL',
                    'required' => true,
                    'type' => 'decimal',
                    'start' => 4,
                    'minLength' => 15,
                    'maxLength' => 15,
                    'minimum' => 0,
                    'maximum' => 99999999999999,
                    'decimals' => 2
                ],
                'peso_total_nf' => [
                    'element' => 'TOTAIS_NF_PESO',
                    'required' => true,
                    'type' => 'decimal',
                    'start' => 19,
                    'minLength' => 15,
                    'maxLength' => 15,
                    'minimum' => 0,
                    'maximum' => 99999999999999,
                    'decimals' => 2
                ],
                'peso_cubado_total_nf' => [
                    'element' => 'TOTAIS_NF_PESO_CUB',
                    'type' => 'decimal',
                    'start' => 34,
                    'minLength' => 15,
                    'maxLength' => 15,
                    'minimum' => 0,
                    'maximum' => 99999999999999,
                    'decimals' => 2
                ],
                'volumes_total_nf' => [
                    'element' => 'TOTAIS_NF_VOLUMES',
                    'type' => 'decimal',
                    'start' => 49,
                    'minLength' => 15,
                    'maxLength' => 15,
                    'minimum' => 0,
                    'maximum' => 99999999999999,
                    'decimals' => 2
                ],
                'valor_total_cobrar' => [
                    'element' => 'TOTAIS_NF_COB_VL',
                    'type' => 'decimal',
                    'start' => 64,
                    'minLength' => 15,
                    'maxLength' => 15,
                    'minimum' => 0,
                    'maximum' => 99999999999999,
                    'decimals' => 2
                ],
                'valor_total_seguro' => [
                    'element' => 'TOTAIS_NF_SEG_VL',
                    'type' => 'decimal',
                    'start' => 79,
                    'minLength' => 15,
                    'maxLength' => 15,
                    'minimum' => 0,
                    'maximum' => 99999999999999,
                    'decimals' => 2
                ],
                'filler' => [
                    'element' => 'FILLER',
                    'type' => 'string',
                    'start' => 94,
                    'minLength' => 147,
                    'maxLength' => 147
                ],
            ]
        ]
    ],
    'extra' => [
        'NF_ICMS_VL_10' => [
            'element' => 'NF_ICMS_VL_10',
            'type' => 'decimal',
            'minimum' => 0,
            'maximum' => 99999999999999,
            'decimals' => 2,
            'sep_dec' => '.',
            'expression' => 'NF_ICMS_VL*NF_ICMS_VL'
        ],
        'NF_ICMS_BOOL' => [
            'element' => 'NF_ICMS_BOOL',
            'type' => 'boolean',
            'expression' => 'NF_ICMS_VL<NF_ICMS_VL_10'
        ],
        'NF_ICMS_FINAL' => [
            'element' => 'NF_ICMS_FINAL',
            'type' => 'decimal',
            'minimum' => 0,
            'maximum' => 99999999999999,
            'decimals' => 2,
            'sep_dec' => '.',
            'expression' => 'NF_ICMS_BOOL?NF_ICMS_VL:NF_ICMS_VL_10'
        ]
    ],
    'enums' => [
        'SIM_NAO_BOOL' => [
            'S' => '1',
            'N' => '0'
        ]
    ]
];
