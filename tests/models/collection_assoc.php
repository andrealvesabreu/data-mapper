<?php

return [
    'parser' => 'collection',
    'setup' => [
        'fillMissing' => false, //Auto fill elements missing that have a default value in mnodel
        'default' => '', //Use for option fillMissing= true and no default value set in field
        'checkInput' => false, //Check data while read from source
        'checkOutput' => false, //Check data while writing to destination
        'length' => 500, //maxlengtt of each field
        'type' => 'assoc', //Mapper is based on key name or numeric index
    ],
    'definitions' => [
        [
            'element' => 'NF_NUMERO',
            'type' => 'integer',
            'minimum' => 0,
            'maximum' => 999999999999999,
            'minLength' => 0,
            'maxLength' => 3
        ],
        [
            'element' => 'NF_SERIE',
            'type' => 'integer',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'NF_CFOP',
            'type' => 'integer',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'NF_PEDIDO',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'NF_VL',
            'type' => 'decimal',
            'decimals' => 2,
            'dec_sep' => '.',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'NF_PROD_VL',
            'type' => 'decimal',
            'decimals' => 2,
            'dec_sep' => '.',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'NF_PESO',
            'type' => 'decimal',
            'decimals' => 2,
            'dec_sep' => '.',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'NF_VOLUMES',
            'type' => 'integer',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'NF_NATUREZA',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'NF_ESPECIE',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'DATA_EMISSAO',
            'type' => 'datetime',
            'format' => 'yyyy-mm-dd hh:ii:ss',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'NF_CHAVE',
            'type' => 'numeric',
            'minLength' => 44,
            'maxLength' => 44
        ],
        [
            'element' => 'NF_TP_OPERACAO',
            'type' => 'enum',
            'enum' => [
                'E',
                'S'
            ]
        ],
        [
            'element' => 'DEST_CNP',
            'type' => 'numeric',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'DEST_IE',
            'type' => 'numeric',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'DEST_XNOME',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'DEST_XFANT',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'DEST_FONE',
            'type' => 'numeric',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'DEST_EMAIL',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 150
        ],
        [
            'element' => 'DEST_XLGR',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'DEST_NRO',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'DEST_XCPL',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'DEST_XBAIRRO',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'DEST_XMUN',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'DEST_CMUN',
            'type' => 'integer',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'DEST_CEP',
            'type' => 'numeric',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'DEST_XUF',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'DEST_CPAIS',
            'type' => 'integer',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'DEST_XPAIS',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'EXPED_CNP',
            'type' => 'numeric',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'EXPED_XLGR',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'EXPED_NRO',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'EXPED_XCPL',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'EXPED_BAIRRO',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'EXPED_XMUN',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'EXPED_CMUN',
            'type' => 'integer',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'EXPED_CEP',
            'type' => 'numeric',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'EXPED_XUF',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'EXPED_CPAIS',
            'type' => 'integer',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'EXPED_XPAIS',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'RECEB_CNP',
            'type' => 'numeric',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'RECEB_XLGR',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'RECEB_NRO',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'RECEB_XCPL',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'RECEB_BAIRRO',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'RECEB_XMUN',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'RECEB_CMUN',
            'type' => 'integer',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'RECEB_CEP',
            'type' => 'numeric',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'RECEB_XUF',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'RECEB_CPAIS',
            'type' => 'integer',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'RECEB_XPAIS',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'EMIT_CNP',
            'type' => 'numeric',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'EMIT_IE',
            'type' => 'numeric',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'EMIT_XNOME',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'EMIT_XFANT',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'EMIT_FONE',
            'type' => 'numeric',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'EMIT_EMAIL',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 150
        ],
        [
            'element' => 'EMIT_XLGR',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'EMIT_NRO',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'EMIT_XCPL',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'EMIT_BAIRRO',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'EMIT_XMUN',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'EMIT_CMUN',
            'type' => 'integer',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'EMIT_CEP',
            'type' => 'numeric',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'EMIT_XUF',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'EMIT_CPAIS',
            'type' => 'integer',
            'minLength' => 0,
            'maxLength' => 100
        ],
        [
            'element' => 'EMIT_XPAIS',
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100
        ]
    ]
];
