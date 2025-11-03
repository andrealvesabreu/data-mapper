<?php

/**
 * This model will be used to output structured NF data a part of a supposed system model
 */

return [
    'parser' => 'xml',
    'setup' => [
        'fillMissing' => false, //Auto fill elements missing that have a default value in mnodel
        'checkInput' => false, //Check data while read from source
        'checkOutput' => false, //Check data while writing to destination
        'default' => null, //Check data while writing to destination
        'pivot' => 'infNF',
        'length' => 500, //maxlenght of each field
        'maxDepth' => 512, //Maximum JSON depth to follow
        'maxItems' => 4096, //maximum number of elements in a line
        'maxRecords' => 15000, //Maximum number of records
    ],
    'definitions' => [
        'NFs' => [
            'minItems' => 1,
            'maxItems' => 1,
            'key' => '',
            'elements' =>
            [
                'NF' => [
                    'minItems' => 1,
                    'maxItems' => 1,
                    'key' => '',
                    'elements' =>
                    [
                        'infNF' => [
                            'minItems' => 1,
                            'maxItems' => 1,
                            'key' => '',
                            'elements' =>
                            [
                                'NF_NUMERO' => [
                                    'element' => 'NF_NUMERO',
                                    'type' => 'integer',
                                    'minimum' => 1,
                                    'maximum' => 999999999
                                ],
                                'NF_SERIE' => [
                                    'element' => 'NF_SERIE',
                                    'type' => 'integer',
                                    'minimum' => 0,
                                    'maximum' => 99
                                ],
                                'NF_CHAVE' => [
                                    'element' => 'NF_CHAVE',
                                    'type' => 'numeric',
                                    'minLength' => 44,
                                    'maxLength' => 44
                                ],
                                'NF_CFOP' => [
                                    'element' => 'NF_CFOP',
                                    'type' => 'numeric',
                                    'minLength' => 4,
                                    'maxLength' => 4
                                ],
                                'NF_PEDIDO' => [
                                    'element' => 'NF_PEDIDO',
                                    'type' => 'string',
                                    'minLength' => 4,
                                    'maxLength' => 15
                                ],
                                'NF_VL' => [
                                    'element' => 'NF_VL',
                                    'type' => 'decimal',
                                    'decimals' => 2,
                                    'dec_sep' => '.',
                                    'minimum' => 0,
                                    'maximum' => 9999999999
                                ],
                                'NF_PROD_VL' => [
                                    'element' => 'NF_PROD_VL',
                                    'type' => 'decimal',
                                    'decimals' => 2,
                                    'dec_sep' => '.',
                                    'minimum' => 0,
                                    'maximum' => 9999999999
                                ],
                                'NF_PESO' => [
                                    'element' => 'NF_PESO',
                                    'type' => 'decimal',
                                    'decimals' => 2,
                                    'dec_sep' => '.',
                                    'minimum' => 0,
                                    'maximum' => 9999999
                                ],
                                'NF_VOLUMES' => [
                                    'element' => 'NF_VOLUMES',
                                    'type' => 'integer',
                                    'minimum' => 0,
                                    'maximum' => 9999999
                                ],
                                'NF_NATUREZA' => [
                                    'element' => 'NF_NATUREZA',
                                    'type' => 'string',
                                    'minLength' => 1,
                                    'maxLength' => 60
                                ],
                                'NF_ESPECIE' => [
                                    'element' => 'NF_ESPECIE',
                                    'type' => 'string',
                                    'minLength' => 1,
                                    'maxLength' => 60
                                ],
                                'DATA_EMISSAO' => [
                                    'element' => 'DATA_EMISSAO',
                                    'type' => 'datetime',
                                    'minLength' => 25,
                                    'maxLength' => 25,
                                    'format' => 'yyyy-mm-dd hh:ii:ss'
                                ]
                            ]
                        ],
                        'emit' => [
                            'minItems' => 1,
                            'maxItems' => 1,
                            'key' => '',
                            'elements' =>
                            [
                                'EMIT_CNP' => [
                                    'element' => 'EMIT_CNP',
                                    'type' => 'numeric',
                                    'nullable' => false,
                                    'minLength' => 11,
                                    'maxLength' => 14
                                ],
                                'EMIT_IE' => [
                                    'element' => 'EMIT_IE',
                                    'nullable' => false,
                                    'type' => 'numeric',
                                    'minLength' => 5,
                                    'maxLength' => 15
                                ],
                                'EMIT_XNOME' => [
                                    'element' => 'EMIT_XNOME',
                                    'nullable' => false,
                                    'type' => 'string',
                                    'minLength' => 1,
                                    'maxLength' => 60
                                ],
                                'EMIT_XFANT' => [
                                    'element' => 'EMIT_XFANT',
                                    'nullable' => false,
                                    'type' => 'string',
                                    'minLength' => 1,
                                    'maxLength' => 60
                                ],
                                'EMIT_FONE' => [
                                    'element' => 'EMIT_FONE',
                                    'nullable' => true,
                                    'type' => 'numeric',
                                    'minLength' => 5,
                                    'maxLength' => 15
                                ],
                                'EMIT_EMAIL' => [
                                    'element' => 'EMIT_EMAIL',
                                    'nullable' => false,
                                    'type' => 'string',
                                    'minLength' => 5,
                                    'maxLength' => 60
                                ],
                                'enderEmit' => [
                                    'minItems' => 1,
                                    'maxItems' => 1,
                                    'key' => '',
                                    'elements' =>
                                    [
                                        'EMIT_XLGR' => [
                                            'element' => 'EMIT_XLGR',
                                            'nullable' => false,
                                            'type' => 'string',
                                            'minLength' => 1,
                                            'maxLength' => 60
                                        ],
                                        'EMIT_NRO' => [
                                            'element' => 'EMIT_NRO',
                                            'nullable' => false,
                                            'type' => 'numeric',
                                            'minLength' => 5,
                                            'maxLength' => 15
                                        ],
                                        'EMIT_XCPL' => [
                                            'element' => 'EMIT_XCPL',
                                            'nullable' => true,
                                            'type' => 'string',
                                            'minLength' => 0,
                                            'maxLength' => 60
                                        ],
                                        'EMIT_BAIRRO' => [
                                            'element' => 'EMIT_BAIRRO',
                                            'nullable' => false,
                                            'type' => 'string',
                                            'minLength' => 5,
                                            'maxLength' => 60
                                        ],
                                        'EMIT_CMUN' => [
                                            'element' => 'EMIT_CMUN',
                                            'nullable' => false,
                                            'type' => 'numeric',
                                            'minLength' => 7,
                                            'maxLength' => 7
                                        ],
                                        'EMIT_XMUN' => [
                                            'element' => 'EMIT_XMUN',
                                            'nullable' => false,
                                            'type' => 'string',
                                            'minLength' => 2,
                                            'maxLength' => 60
                                        ],
                                        'EMIT_CEP' => [
                                            'element' => 'EMIT_CEP',
                                            'nullable' => false,
                                            'type' => 'numeric',
                                            'minLength' => 8,
                                            'maxLength' => 8
                                        ],
                                        'EMIT_XUF' => [
                                            'element' => 'EMIT_XUF',
                                            'nullable' => false,
                                            'type' => 'string',
                                            'minLength' => 2,
                                            'maxLength' => 2
                                        ],
                                        'EMIT_CPAIS' => [
                                            'element' => 'EMIT_CPAIS',
                                            'nullable' => false,
                                            'type' => 'integer',
                                            'minimum' => 1,
                                            'maximum' => 10000
                                        ],
                                        'EMIT_XPAIS' => [
                                            'element' => 'EMIT_XPAIS',
                                            'nullable' => false,
                                            'type' => 'string',
                                            'minLength' => 3,
                                            'maxLength' => 40
                                        ],
                                    ]
                                ]
                            ]
                        ],
                        'dest' => [
                            'minItems' => 1,
                            'maxItems' => 1,
                            'key' => '',
                            'elements' =>
                            [
                                'DEST_CNP' => [
                                    'element' => 'DEST_CNP',
                                    'type' => 'numeric',
                                    'nullable' => false,
                                    'minLength' => 11,
                                    'maxLength' => 14
                                ],
                                'DEST_IE' => [
                                    'element' => 'DEST_IE',
                                    'nullable' => false,
                                    'type' => 'numeric',
                                    'minLength' => 5,
                                    'maxLength' => 15
                                ],
                                'DEST_XNOME' => [
                                    'element' => 'DEST_XNOME',
                                    'nullable' => false,
                                    'type' => 'string',
                                    'minLength' => 1,
                                    'maxLength' => 60
                                ],
                                'DEST_XFANT' => [
                                    'element' => 'DEST_XFANT',
                                    'nullable' => false,
                                    'type' => 'string',
                                    'minLength' => 1,
                                    'maxLength' => 60
                                ],
                                'DEST_FONE' => [
                                    'element' => 'DEST_FONE',
                                    'nullable' => true,
                                    'type' => 'numeric',
                                    'minLength' => 5,
                                    'maxLength' => 15
                                ],
                                'DEST_EMAIL' => [
                                    'element' => 'DEST_EMAIL',
                                    'nullable' => false,
                                    'type' => 'string',
                                    'minLength' => 5,
                                    'maxLength' => 60
                                ],
                                'DEST_ENDER' => [
                                    'minItems' => 1,
                                    'maxItems' => 1,
                                    'key' => '',
                                    'elements' =>
                                    [
                                        'DEST_XLGR' => [
                                            'element' => 'DEST_XLGR',
                                            'nullable' => false,
                                            'type' => 'string',
                                            'minLength' => 1,
                                            'maxLength' => 60
                                        ],
                                        'DEST_NRO' => [
                                            'element' => 'DEST_NRO',
                                            'nullable' => false,
                                            'type' => 'numeric',
                                            'minLength' => 5,
                                            'maxLength' => 15
                                        ],
                                        'DEST_XCPL' => [
                                            'element' => 'DEST_XCPL',
                                            'nullable' => true,
                                            'type' => 'string',
                                            'minLength' => 0,
                                            'maxLength' => 60
                                        ],
                                        'DEST_XBAIRRO' => [
                                            'element' => 'DEST_XBAIRRO',
                                            'nullable' => false,
                                            'type' => 'string',
                                            'minLength' => 5,
                                            'maxLength' => 60
                                        ],
                                        'DEST_CMUN' => [
                                            'element' => 'DEST_CMUN',
                                            'nullable' => false,
                                            'type' => 'numeric',
                                            'minLength' => 7,
                                            'maxLength' => 7
                                        ],
                                        'DEST_XMUN' => [
                                            'element' => 'DEST_XMUN',
                                            'nullable' => false,
                                            'type' => 'string',
                                            'minLength' => 2,
                                            'maxLength' => 60
                                        ],
                                        'DEST_CEP' => [
                                            'element' => 'DEST_CEP',
                                            'nullable' => false,
                                            'type' => 'numeric',
                                            'minLength' => 8,
                                            'maxLength' => 8
                                        ],
                                        'DEST_XUF' => [
                                            'element' => 'DEST_XUF',
                                            'nullable' => false,
                                            'type' => 'string',
                                            'minLength' => 2,
                                            'maxLength' => 2
                                        ],
                                        'DEST_CPAIS' => [
                                            'element' => 'DEST_CPAIS',
                                            'nullable' => false,
                                            'type' => 'integer',
                                            'minimum' => 1,
                                            'maximum' => 10000
                                        ],
                                        'DEST_XPAIS' => [
                                            'element' => 'DEST_XPAIS',
                                            'nullable' => false,
                                            'type' => 'string',
                                            'minLength' => 3,
                                            'maxLength' => 40
                                        ],
                                    ]
                                ]
                            ]
                        ],
                        'retira' => [
                            'minItems' => 1,
                            'maxItems' => 1,
                            'key' => '',
                            'elements' =>
                            [
                                'EXPED_CNP' => [
                                    'element' => 'EXPED_CNP',
                                    'type' => 'numeric',
                                    'nullable' => false,
                                    'minLength' => 11,
                                    'maxLength' => 14
                                ],
                                'EXPED_XLGR' => [
                                    'element' => 'EXPED_XLGR',
                                    'nullable' => false,
                                    'type' => 'string',
                                    'minLength' => 1,
                                    'maxLength' => 60
                                ],
                                'EXPED_NRO' => [
                                    'element' => 'EXPED_NRO',
                                    'nullable' => false,
                                    'type' => 'numeric',
                                    'minLength' => 5,
                                    'maxLength' => 15
                                ],
                                'EXPED_XCPL' => [
                                    'element' => 'EXPED_XCPL',
                                    'nullable' => true,
                                    'type' => 'string',
                                    'minLength' => 0,
                                    'maxLength' => 60
                                ],
                                'EXPED_BAIRRO' => [
                                    'element' => 'EXPED_BAIRRO',
                                    'nullable' => false,
                                    'type' => 'string',
                                    'minLength' => 5,
                                    'maxLength' => 60
                                ],
                                'EXPED_CMUN' => [
                                    'element' => 'EXPED_CMUN',
                                    'nullable' => false,
                                    'type' => 'numeric',
                                    'minLength' => 7,
                                    'maxLength' => 7
                                ],
                                'EXPED_XMUN' => [
                                    'element' => 'EXPED_XMUN',
                                    'nullable' => false,
                                    'type' => 'string',
                                    'minLength' => 2,
                                    'maxLength' => 60
                                ],
                                'EXPED_CEP' => [
                                    'element' => 'EXPED_CEP',
                                    'nullable' => false,
                                    'type' => 'numeric',
                                    'minLength' => 8,
                                    'maxLength' => 8
                                ],
                                'EXPED_XUF' => [
                                    'element' => 'EXPED_XUF',
                                    'nullable' => false,
                                    'type' => 'string',
                                    'minLength' => 2,
                                    'maxLength' => 2
                                ],
                                'EXPED_CPAIS' => [
                                    'element' => 'EXPED_CPAIS',
                                    'nullable' => false,
                                    'type' => 'integer',
                                    'minimum' => 1,
                                    'maximum' => 10000
                                ],
                                'EXPED_XPAIS' => [
                                    'element' => 'EXPED_XPAIS',
                                    'nullable' => false,
                                    'type' => 'string',
                                    'minLength' => 3,
                                    'maxLength' => 40
                                ]
                            ]
                        ],
                        'entrega' => [
                            'minItems' => 1,
                            'maxItems' => 1,
                            'key' => '',
                            'elements' =>
                            [
                                'RECEB_CNP' => [
                                    'element' => 'RECEB_CNP',
                                    'type' => 'numeric',
                                    'nullable' => false,
                                    'minLength' => 11,
                                    'maxLength' => 14
                                ],
                                'RECEB_XLGR' => [
                                    'element' => 'RECEB_XLGR',
                                    'nullable' => false,
                                    'type' => 'string',
                                    'minLength' => 1,
                                    'maxLength' => 60
                                ],
                                'RECEB_NRO' => [
                                    'element' => 'RECEB_NRO',
                                    'nullable' => false,
                                    'type' => 'numeric',
                                    'minLength' => 5,
                                    'maxLength' => 15
                                ],
                                'RECEB_XCPL' => [
                                    'element' => 'RECEB_XCPL',
                                    'nullable' => true,
                                    'type' => 'string',
                                    'minLength' => 0,
                                    'maxLength' => 60
                                ],
                                'RECEB_BAIRRO' => [
                                    'element' => 'RECEB_BAIRRO',
                                    'nullable' => false,
                                    'type' => 'string',
                                    'minLength' => 5,
                                    'maxLength' => 60
                                ],
                                'RECEB_CMUN' => [
                                    'element' => 'RECEB_CMUN',
                                    'nullable' => false,
                                    'type' => 'numeric',
                                    'minLength' => 7,
                                    'maxLength' => 7
                                ],
                                'RECEB_XMUN' => [
                                    'element' => 'RECEB_XMUN',
                                    'nullable' => false,
                                    'type' => 'string',
                                    'minLength' => 2,
                                    'maxLength' => 60
                                ],
                                'RECEB_CEP' => [
                                    'element' => 'RECEB_CEP',
                                    'nullable' => false,
                                    'type' => 'numeric',
                                    'minLength' => 8,
                                    'maxLength' => 8
                                ],
                                'RECEB_XUF' => [
                                    'element' => 'RECEB_XUF',
                                    'nullable' => false,
                                    'type' => 'string',
                                    'minLength' => 2,
                                    'maxLength' => 2
                                ],
                                'RECEB_CPAIS' => [
                                    'element' => 'RECEB_CPAIS',
                                    'nullable' => false,
                                    'type' => 'integer',
                                    'minimum' => 1,
                                    'maximum' => 10000
                                ],
                                'RECEB_XPAIS' => [
                                    'element' => 'RECEB_XPAIS',
                                    'nullable' => false,
                                    'type' => 'string',
                                    'minLength' => 3,
                                    'maxLength' => 40
                                ]
                            ]
                        ],

                    ]
                ]
            ]
        ]
    ]
];
