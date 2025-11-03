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
        'pivot' => 'infNFe',
        'length' => 500, //maxlenght of each field
        'maxDepth' => 512, //Maximum JSON depth to follow
        'maxItems' => 4096, //maximum number of elements in a line
        'maxRecords' => 15000, //Maximum number of records
    ],
    'definitions' => [
        'nfeProc' => [
            'minItems' => 1,
            'maxItems' => 1,
            'key' => '',
            'elements' =>
            [
                'NFe' => [
                    'minItems' => 1,
                    'maxItems' => 1,
                    'key' => '',
                    'elements' =>
                    [
                        'infNFe' => [
                            'minItems' => 1,
                            'maxItems' => 1,
                            'key' => '',
                            'elements' =>
                            [
                                'ide' => [
                                    'minItems' => 1,
                                    'maxItems' => 1,
                                    'key' => '',
                                    'elements' =>
                                    [
                                        'nNF' => [
                                            'element' => 'NF_NUMERO',
                                            'type' => 'integer',
                                            'minimum' => 1,
                                            'maximum' => 999999999
                                        ],
                                        'serie' => [
                                            'element' => 'NF_SERIE',
                                            'type' => 'integer',
                                            'minimum' => 0,
                                            'maximum' => 99
                                        ],
                                        'chNFe' => [
                                            'element' => 'NF_CHAVE',
                                            'type' => 'numeric',
                                            'minLength' => 44,
                                            'maxLength' => 44
                                        ],
                                        'CFOP' => [
                                            'element' => 'NF_CFOP',
                                            'type' => 'numeric',
                                            'minLength' => 4,
                                            'maxLength' => 4
                                        ],
                                        'xPed' => [
                                            'element' => 'NF_PEDIDO',
                                            'type' => 'string',
                                            'minLength' => 4,
                                            'maxLength' => 15
                                        ],
                                        'vlNF' => [
                                            'element' => 'NF_VL',
                                            'type' => 'decimal',
                                            'decimals' => 2,
                                            'dec_sep' => '.',
                                            'minimum' => 0,
                                            'maximum' => 9999999999
                                        ],
                                        'vlProd' => [
                                            'element' => 'NF_PROD_VL',
                                            'type' => 'decimal',
                                            'decimals' => 2,
                                            'dec_sep' => '.',
                                            'minimum' => 0,
                                            'maximum' => 9999999999
                                        ],
                                        'peso' => [
                                            'element' => 'NF_PESO',
                                            'type' => 'decimal',
                                            'decimals' => 2,
                                            'dec_sep' => '.',
                                            'minimum' => 0,
                                            'maximum' => 9999999
                                        ],
                                        'volumes' => [
                                            'element' => 'NF_VOLUMES',
                                            'type' => 'integer',
                                            'minimum' => 0,
                                            'maximum' => 9999999
                                        ],
                                        'xNat' => [
                                            'element' => 'NF_NATUREZA',
                                            'type' => 'string',
                                            'minLength' => 1,
                                            'maxLength' => 60
                                        ],
                                        'xEsp' => [
                                            'element' => 'NF_ESPECIE',
                                            'type' => 'string',
                                            'minLength' => 1,
                                            'maxLength' => 60
                                        ],
                                        'dhEmi' => [
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
                                        'CNP' => [
                                            'element' => 'EMIT_CNP',
                                            'type' => 'numeric',
                                            'nullable' => false,
                                            'minLength' => 11,
                                            'maxLength' => 14
                                        ],
                                        'IE' => [
                                            'element' => 'EMIT_IE',
                                            'nullable' => false,
                                            'type' => 'numeric',
                                            'minLength' => 5,
                                            'maxLength' => 15
                                        ],
                                        'xNome' => [
                                            'element' => 'EMIT_XNOME',
                                            'nullable' => false,
                                            'type' => 'string',
                                            'minLength' => 1,
                                            'maxLength' => 60
                                        ],
                                        'xFant' => [
                                            'element' => 'EMIT_XFANT',
                                            'nullable' => false,
                                            'type' => 'string',
                                            'minLength' => 1,
                                            'maxLength' => 60
                                        ],
                                        'fone' => [
                                            'element' => 'EMIT_FONE',
                                            'nullable' => true,
                                            'type' => 'numeric',
                                            'minLength' => 5,
                                            'maxLength' => 15
                                        ],
                                        'email' => [
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
                                                'xLgr' => [
                                                    'element' => 'EMIT_XLGR',
                                                    'nullable' => false,
                                                    'type' => 'string',
                                                    'minLength' => 1,
                                                    'maxLength' => 60
                                                ],
                                                'nro' => [
                                                    'element' => 'EMIT_NRO',
                                                    'nullable' => false,
                                                    'type' => 'numeric',
                                                    'minLength' => 5,
                                                    'maxLength' => 15
                                                ],
                                                'xCpl' => [
                                                    'element' => 'EMIT_XCPL',
                                                    'nullable' => true,
                                                    'type' => 'string',
                                                    'minLength' => 0,
                                                    'maxLength' => 60
                                                ],
                                                'xBairro' => [
                                                    'element' => 'EMIT_BAIRRO',
                                                    'nullable' => false,
                                                    'type' => 'string',
                                                    'minLength' => 5,
                                                    'maxLength' => 60
                                                ],
                                                'cMun' => [
                                                    'element' => 'EMIT_CMUN',
                                                    'nullable' => false,
                                                    'type' => 'numeric',
                                                    'minLength' => 7,
                                                    'maxLength' => 7
                                                ],
                                                'xMun' => [
                                                    'element' => 'EMIT_XMUN',
                                                    'nullable' => false,
                                                    'type' => 'string',
                                                    'minLength' => 2,
                                                    'maxLength' => 60
                                                ],
                                                'CEP' => [
                                                    'element' => 'EMIT_CEP',
                                                    'nullable' => false,
                                                    'type' => 'numeric',
                                                    'minLength' => 8,
                                                    'maxLength' => 8
                                                ],
                                                'UF' => [
                                                    'element' => 'EMIT_XUF',
                                                    'nullable' => false,
                                                    'type' => 'string',
                                                    'minLength' => 2,
                                                    'maxLength' => 2
                                                ],
                                                'cPais' => [
                                                    'element' => 'EMIT_CPAIS',
                                                    'nullable' => false,
                                                    'type' => 'integer',
                                                    'minimum' => 1,
                                                    'maximum' => 10000
                                                ],
                                                'xPais' => [
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
                                        'CNP' => [
                                            'element' => 'DEST_CNP',
                                            'type' => 'numeric',
                                            'nullable' => false,
                                            'minLength' => 11,
                                            'maxLength' => 14
                                        ],
                                        'IE' => [
                                            'element' => 'DEST_IE',
                                            'nullable' => false,
                                            'type' => 'numeric',
                                            'minLength' => 5,
                                            'maxLength' => 15
                                        ],
                                        'xNome' => [
                                            'element' => 'DEST_XNOME',
                                            'nullable' => false,
                                            'type' => 'string',
                                            'minLength' => 1,
                                            'maxLength' => 60
                                        ],
                                        'xFant' => [
                                            'element' => 'DEST_XFANT',
                                            'nullable' => false,
                                            'type' => 'string',
                                            'minLength' => 1,
                                            'maxLength' => 60
                                        ],
                                        'fone' => [
                                            'element' => 'DEST_FONE',
                                            'nullable' => true,
                                            'type' => 'numeric',
                                            'minLength' => 5,
                                            'maxLength' => 15
                                        ],
                                        'email' => [
                                            'element' => 'DEST_EMAIL',
                                            'nullable' => false,
                                            'type' => 'string',
                                            'minLength' => 5,
                                            'maxLength' => 60
                                        ],
                                        'enderDest' => [
                                            'minItems' => 1,
                                            'maxItems' => 1,
                                            'key' => '',
                                            'elements' =>
                                            [
                                                'xLgr' => [
                                                    'element' => 'DEST_XLGR',
                                                    'nullable' => false,
                                                    'type' => 'string',
                                                    'minLength' => 1,
                                                    'maxLength' => 60
                                                ],
                                                'nro' => [
                                                    'element' => 'DEST_NRO',
                                                    'nullable' => false,
                                                    'type' => 'numeric',
                                                    'minLength' => 5,
                                                    'maxLength' => 15
                                                ],
                                                'xCpl' => [
                                                    'element' => 'DEST_XCPL',
                                                    'nullable' => true,
                                                    'type' => 'string',
                                                    'minLength' => 0,
                                                    'maxLength' => 60
                                                ],
                                                'xBairro' => [
                                                    'element' => 'DEST_XBAIRRO',
                                                    'nullable' => false,
                                                    'type' => 'string',
                                                    'minLength' => 5,
                                                    'maxLength' => 60
                                                ],
                                                'cMun' => [
                                                    'element' => 'DEST_CMUN',
                                                    'nullable' => false,
                                                    'type' => 'numeric',
                                                    'minLength' => 7,
                                                    'maxLength' => 7
                                                ],
                                                'xMun' => [
                                                    'element' => 'DEST_XMUN',
                                                    'nullable' => false,
                                                    'type' => 'string',
                                                    'minLength' => 2,
                                                    'maxLength' => 60
                                                ],
                                                'CEP' => [
                                                    'element' => 'DEST_CEP',
                                                    'nullable' => false,
                                                    'type' => 'numeric',
                                                    'minLength' => 8,
                                                    'maxLength' => 8
                                                ],
                                                'UF' => [
                                                    'element' => 'DEST_XUF',
                                                    'nullable' => false,
                                                    'type' => 'string',
                                                    'minLength' => 2,
                                                    'maxLength' => 2
                                                ],
                                                'cPais' => [
                                                    'element' => 'DEST_CPAIS',
                                                    'nullable' => false,
                                                    'type' => 'integer',
                                                    'minimum' => 1,
                                                    'maximum' => 10000
                                                ],
                                                'xPais' => [
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
                                        'CNP' => [
                                            'element' => 'EXPED_CNP',
                                            'type' => 'numeric',
                                            'nullable' => false,
                                            'minLength' => 11,
                                            'maxLength' => 14
                                        ],
                                        'xLgr' => [
                                            'element' => 'EXPED_XLGR',
                                            'nullable' => false,
                                            'type' => 'string',
                                            'minLength' => 1,
                                            'maxLength' => 60
                                        ],
                                        'nro' => [
                                            'element' => 'EXPED_NRO',
                                            'nullable' => false,
                                            'type' => 'numeric',
                                            'minLength' => 5,
                                            'maxLength' => 15
                                        ],
                                        'xCpl' => [
                                            'element' => 'EXPED_XCPL',
                                            'nullable' => true,
                                            'type' => 'string',
                                            'minLength' => 0,
                                            'maxLength' => 60
                                        ],
                                        'xBairro' => [
                                            'element' => 'EXPED_BAIRRO',
                                            'nullable' => false,
                                            'type' => 'string',
                                            'minLength' => 5,
                                            'maxLength' => 60
                                        ],
                                        'cMun' => [
                                            'element' => 'EXPED_CMUN',
                                            'nullable' => false,
                                            'type' => 'numeric',
                                            'minLength' => 7,
                                            'maxLength' => 7
                                        ],
                                        'xMun' => [
                                            'element' => 'EXPED_XMUN',
                                            'nullable' => false,
                                            'type' => 'string',
                                            'minLength' => 2,
                                            'maxLength' => 60
                                        ],
                                        'CEP' => [
                                            'element' => 'EXPED_CEP',
                                            'nullable' => false,
                                            'type' => 'numeric',
                                            'minLength' => 8,
                                            'maxLength' => 8
                                        ],
                                        'UF' => [
                                            'element' => 'EXPED_XUF',
                                            'nullable' => false,
                                            'type' => 'string',
                                            'minLength' => 2,
                                            'maxLength' => 2
                                        ],
                                        'cPais' => [
                                            'element' => 'EXPED_CPAIS',
                                            'nullable' => false,
                                            'type' => 'integer',
                                            'minimum' => 1,
                                            'maximum' => 10000
                                        ],
                                        'xPais' => [
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
                                        'CNP' => [
                                            'element' => 'RECEB_CNP',
                                            'type' => 'numeric',
                                            'nullable' => false,
                                            'minLength' => 11,
                                            'maxLength' => 14
                                        ],
                                        'xLgr' => [
                                            'element' => 'RECEB_XLGR',
                                            'nullable' => false,
                                            'type' => 'string',
                                            'minLength' => 1,
                                            'maxLength' => 60
                                        ],
                                        'nro' => [
                                            'element' => 'RECEB_NRO',
                                            'nullable' => false,
                                            'type' => 'numeric',
                                            'minLength' => 5,
                                            'maxLength' => 15
                                        ],
                                        'xCpl' => [
                                            'element' => 'RECEB_XCPL',
                                            'nullable' => true,
                                            'type' => 'string',
                                            'minLength' => 0,
                                            'maxLength' => 60
                                        ],
                                        'xBairro' => [
                                            'element' => 'RECEB_BAIRRO',
                                            'nullable' => false,
                                            'type' => 'string',
                                            'minLength' => 5,
                                            'maxLength' => 60
                                        ],
                                        'cMun' => [
                                            'element' => 'RECEB_CMUN',
                                            'nullable' => false,
                                            'type' => 'numeric',
                                            'minLength' => 7,
                                            'maxLength' => 7
                                        ],
                                        'xMun' => [
                                            'element' => 'RECEB_XMUN',
                                            'nullable' => false,
                                            'type' => 'string',
                                            'minLength' => 2,
                                            'maxLength' => 60
                                        ],
                                        'CEP' => [
                                            'element' => 'RECEB_CEP',
                                            'nullable' => false,
                                            'type' => 'numeric',
                                            'minLength' => 8,
                                            'maxLength' => 8
                                        ],
                                        'UF' => [
                                            'element' => 'RECEB_XUF',
                                            'nullable' => false,
                                            'type' => 'string',
                                            'minLength' => 2,
                                            'maxLength' => 2
                                        ],
                                        'cPais' => [
                                            'element' => 'RECEB_CPAIS',
                                            'nullable' => false,
                                            'type' => 'integer',
                                            'minimum' => 1,
                                            'maximum' => 10000
                                        ],
                                        'xPais' => [
                                            'element' => 'RECEB_XPAIS',
                                            'nullable' => false,
                                            'type' => 'string',
                                            'minLength' => 3,
                                            'maxLength' => 40
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];
