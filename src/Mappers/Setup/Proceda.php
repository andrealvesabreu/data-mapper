<?php

declare(strict_types=1);

namespace Inspire\DataMapper\Mappers\Setup;

use Inspire\DataMapper\Exceptions\SetupException;
use Inspire\DataMapper\DataType\{
    Datetime,
    Decimal,
    Enum,
    Integer,
    Numeric,
    Str
};

/**
 * Classe para configurações de parser do tipo Proceda
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
final class Proceda extends Main
{

    /**
     * Propriedades configuráveis deste parser
     * Pode ser usando como refencia para criação de UI
     */
    const PROPERTIES = [
        //Tipos de dados e configurações aplicáveis a cada um
        'type' => [
            'datetime' => [
                ...Datetime::PROPERTIES,
                'start' => [
                    'type' => 'int',
                    'minimum' => 1,
                    'maximum' => 1000,
                    'required' => true
                ],
                'filler' => [
                    'type' => 'string',
                    'minimum' => 1,
                    'maximum' => 1
                ],
                'align' => [
                    'type' => 'choice',
                    'default' => 'left',
                    'options' => [
                        'right',
                        'left'
                    ]
                ],
                'minLength' => [
                    ...Datetime::PROPERTIES['minLength'],
                    'required' => true
                ]
            ],
            'decimal' => [
                ...Decimal::PROPERTIES,
                'start' => [
                    'type' => 'int',
                    'required' => true,
                    'minimum' => 1,
                    'maximum' => 1000
                ],
                'filler' => [
                    'type' => 'string',
                    'minimum' => 1,
                    'maximum' => 1
                ],
                'align' => [
                    'type' => 'choice',
                    'default' => 'right',
                    'options' => [
                        'right',
                        'left'
                    ]
                ],
                'minLength' => [
                    ...Datetime::PROPERTIES['minLength'],
                    'required' => true
                ]
            ],
            'enum' => [
                ...Enum::PROPERTIES,
                'start' => [
                    'type' => 'int',
                    'minimum' => 1,
                    'required' => true,
                    'maximum' => 1000
                ],
                'filler' => [
                    'type' => 'string',
                    'minimum' => 1,
                    'maximum' => 1
                ],
                'align' => [
                    'type' => 'choice',
                    'default' => 'left',
                    'options' => [
                        'right',
                        'left'
                    ]
                ],
                'minLength' => [
                    ...Datetime::PROPERTIES['minLength'],
                    'required' => true
                ]
            ],
            'integer' => [
                ...Integer::PROPERTIES,
                'start' => [
                    'type' => 'int',
                    'minimum' => 1,
                    'required' => true,
                    'maximum' => 1000
                ],
                'filler' => [
                    'type' => 'string',
                    'minimum' => 1,
                    'maximum' => 1
                ],
                'align' => [
                    'type' => 'choice',
                    'default' => 'right',
                    'options' => [
                        'right',
                        'left'
                    ]
                ],
                'minLength' => [
                    ...Datetime::PROPERTIES['minLength'],
                    'required' => true
                ]
            ],
            'numeric' => [
                ...Numeric::PROPERTIES,
                'start' => [
                    'type' => 'int',
                    'minimum' => 1,
                    'required' => true,
                    'maximum' => 1000
                ],
                'filler' => [
                    'type' => 'string',
                    'minimum' => 1,
                    'maximum' => 1
                ],
                'align' => [
                    'type' => 'choice',
                    'default' => 'left',
                    'options' => [
                        'right',
                        'left'
                    ]
                ],
                'minLength' => [
                    ...Datetime::PROPERTIES['minLength'],
                    'required' => true
                ]
            ],
            'string' => [
                ...Str::PROPERTIES,
                'start' => [
                    'type' => 'int',
                    'minimum' => 1,
                    'required' => true,
                    'maximum' => 1000
                ],
                'filler' => [
                    'type' => 'string',
                    'minimum' => 1,
                    'maximum' => 1
                ],
                'align' => [
                    'type' => 'choice',
                    'default' => 'left',
                    'options' => [
                        'right',
                        'left'
                    ]
                ],
                'minLength' => [
                    ...Datetime::PROPERTIES['minLength'],
                    'required' => true
                ]
            ],
        ],
        //Configurações de parser
        'setup' => [
            'fillMissing' => [
                'type' => 'bool',
                'required' => true,
                'default' => false
            ],
            'checkInput' => [
                'type' => 'bool',
                'required' => true,
                'default' => false
            ],
            'checkOutput' => [
                'type' => 'bool',
                'required' => true,
                'default' => false
            ],
            'length' => [
                'type' => 'int',
                'default' => 0,
                'minimum' => 0,
                'maximum' => 4096
            ],
            'fillerInteger' => [
                'type' => 'string',
                'default' => 0,
                'minimum' => 1,
                'maximum' => 1
            ],
            'fillerDecimal' => [
                'type' => 'string',
                'default' => 0,
                'minimum' => 1,
                'maximum' => 1
            ],
            'fillerEnum' => [
                'type' => 'string',
                'default' => ' ',
                'minimum' => 1,
                'maximum' => 1
            ],
            'fillerStr' => [
                'type' => 'string',
                'default' => ' ',
                'minimum' => 1,
                'maximum' => 1
            ],
            'fillerDatetime' => [
                'type' => 'string',
                'default' => ' ',
                'minimum' => 1,
                'maximum' => 1
            ],
            'fillerNumeric' => [
                'type' => 'string',
                'default' => ' ',
                'minimum' => 1,
                'maximum' => 1
            ],
            'alignInteger' => [
                'type' => 'choice',
                'default' => 'right',
                'options' => [
                    'right',
                    'left'
                ]
            ],
            'alignDecimal' => [
                'type' => 'choice',
                'default' => 'right',
                'options' => [
                    'right',
                    'left'
                ]
            ],
            'alignEnum' => [
                'type' => 'choice',
                'default' => 'left',
                'options' => [
                    'right',
                    'left'
                ]
            ],
            'alignStr' => [
                'type' => 'choice',
                'default' => 'left',
                'options' => [
                    'right',
                    'left'
                ]
            ],
            'alignDatetime' => [
                'type' => 'choice',
                'default' => 'left',
                'options' => [
                    'right',
                    'left'
                ]
            ],
            'alignNumeric' => [
                'type' => 'choice',
                'default' => 'left',
                'options' => [
                    'right',
                    'left'
                ]
            ],
            'maxItems' => [
                'type' => 'int',
                'default' => 4096,
                'minimum' => 0,
                'maximum' => 10000
            ],
            'maxRecords' => [
                'type' => 'int',
                'default' => 10000,
                'minimum' => 0,
                'maximum' => 50000
            ],
            'default' => [
                'type' => 'string',
                'default' => '',
                'minLength' => 0,
                'maxLength' => 100
            ],
            'pivot' => [
                'type' => 'string',
                'default' => null,
                'minLength' => 0,
                'maxLength' => 100
            ]
        ]
    ];

    /**
     * @var array
     */
    private array $mapType = [
        'datetime' => 'datetime',
        'decimal' => 'float',
        'integer' => 'integer',
        'enum' => 'enum',
        'number' => 'number',
        'str' => 'string'
    ];

    /**
     * @param string $type
     *
     * @return string
     */
    public function getInternalType(string $type): string
    {
        return array_flip($this->mapType)[$type] ?? 'str';
    }

    /**
     * Propriedade para definir se os campos ausentes devem ser preenchidos.
     * Esta ptopriedade pode ser bastante problemática para modelos baseados em indices numéricos
     *
     * @var bool
     */
    protected bool $fillMissing = true;

    /**
     * Propriedade para definir se os dados de entrada devem ser validados durante a leitura da fonte de dados
     *
     * @var bool
     */
    protected bool $checkInput = true;

    /**
     * Propriedade para definir se os dados de saída devem ser validados durante a compilação dos dados
     *
     * @var bool
     */
    protected bool $checkOutput = true;

    /**
     * Propriedade para definirir um limite de comprimento de campo padrão, caso este não seja especificado no elemento
     *
     * @var int|null
     */
    protected ?int $length = null;

    /**
     * Propriedade para definirir o caracter de preenchimento para cada tipo
     *
     * @var array
     */
    protected array $filler = [];

    /**
     * Propriedade para definirir o alinhamento de valor para cada tipo
     *
     * @var array
     */
    protected array $align = [];

    /**
     * Propriedade para definir o elemento central do elemento/entidade do modelo
     * Este campo é a referencia para o parser criar um novo objeto para toda ocorrencia dele e criar todas as propriedades relacionadas para cada uma de suas ocorrencias
     *
     * @var string|null
     */
    protected ?string $pivot = null;

    /**
     * Propriedade para definir um valor default a ser utilizado em campos que não tenham essa propriedade definida explicitamente
     *
     * @var string|null
     */
    protected ?string $default = null;

    /**
     * Propriedade para definir o número máximo de propriedades de cada objeto
     *
     * @var int
     */
    protected int $maxItems = 4096;

    /**
     * Propriedade para definir o número máximo de objetos gerados na extração
     *
     * @var int
     */
    protected int $maxRecords = 10000;

    /**
     * Retorna valor da propriedade fillMissing
     *
     * @return bool
     */
    public function isFillMissing(): bool
    {
        return $this->fillMissing;
    }

    /**
     * Retorna valor da propriedade checkInput
     *
     * @return bool
     */
    public function isCheckInput(): bool
    {
        return $this->checkInput;
    }

    /**
     * Define o valor da propriedade checkInput
     *
     * @param bool $check Valor a atribuir
     *
     * @return Csv
     */
    public function setCheckInput(bool $check): Proceda
    {
        $this->checkInput = $check;
        return $this;
    }

    /**
     * Retorna valor da propriedade checkOutput
     *
     * @return bool
     */
    public function isCheckOutput(): bool
    {
        return $this->checkOutput;
    }

    /**
     * Define o valor da propriedade checkInput
     *
     * @param bool $check Valor a definir
     *
     * @return Csv
     */
    public function setCheckOutput(bool $check): Proceda
    {
        $this->checkOutput = $check;
        return $this;
    }

    /**
     * Retorna valor da propriedade length
     *
     * @return int|null
     */
    public function getLength(): ?int
    {
        return $this->length;
    }

    /**
     * Get the value of filler
     */
    public function getFiller(string $type, string $default = ' '): string
    {
        return $this->filler[$type] ?? $this->filler[$this->mapType[$type]] ?? $default;
    }

    /**
     * Get the value of align
     */
    public function getAlign(string $type, string $default = 'left'): int
    {
        switch ($this->align[$type] ?? $this->align[$this->mapType[$type]] ?? $default) {
            case 'left':
                return STR_PAD_RIGHT;
            case 'right':
                return STR_PAD_LEFT;
            case 'both':
                return STR_PAD_BOTH;
        };
        return STR_PAD_LEFT;
    }

    /**
     * Retorna valor da propriedade default
     *
     * @return null|string
     */
    public function getDefault(): ?string
    {
        return $this->default;
    }

    /**
     * Retorna o elemento pivo
     *
     * @return  string|null
     */
    public function getPivot(): ?string
    {
        return $this->pivot;
    }

    /**
     * Retorna limite de propriedades em um objeto
     *
     * @return  int
     */
    public function getMaxItems()
    {
        return $this->maxItems;
    }

    /**
     * Retorna limite de objetos no datasource
     *
     * @return  int
     */
    public function getMaxRecords()
    {
        return $this->maxRecords;
    }

    /**
     * Método para validar configuração
     *
     * @return bool
     */
    public function check(): bool
    {
        if (
            $this->pivot == null
        ) {
            throw new SetupException("You must set a pivot element.");
        }
        return true;
    }
}
