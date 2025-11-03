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
 * Classe para configurações de parser do tipo Json
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
class Json extends Main
{

    /**
     * Propriedades configuráveis deste parser
     * Pode ser usando como refencia para criação de UI
     */
    const PROPERTIES = [
        //Tipos de dados e configurações aplocáveis a cada um
        'type' => [
            'datetime' => Datetime::PROPERTIES,
            'decimal' => Decimal::PROPERTIES,
            'enum' => Enum::PROPERTIES,
            'integer' => Integer::PROPERTIES,
            'numeric' => Numeric::PROPERTIES,
            'string' => Str::PROPERTIES,
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
                'required' => false,
                'default' => 0,
                'minimum' => 0,
                'maximum' => 4096
            ],
            'maxItems' => [
                'type' => 'int',
                'required' => false,
                'default' => 4096,
                'minimum' => 0,
                'maximum' => 10000
            ],
            'maxRecords' => [
                'type' => 'int',
                'required' => false,
                'default' => 10000,
                'minimum' => 0,
                'maximum' => 50000
            ],
            'maxDepth' => [
                'type' => 'int',
                'required' => false,
                'default' => 512,
                'minimum' => 0,
                'maximum' => 512
            ],
            'default' => [
                'type' => 'string',
                'required' => true,
                'default' => '',
                'minLength' => 0,
                'maxLength' => 100
            ],
            'pivot' => [
                'type' => 'string',
                'required' => true,
                'default' => null,
                'minLength' => 0,
                'maxLength' => 100
            ]
        ]
    ];

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
     * Profundidade máxima de elementos na hierarquia. Interrompers processamento se o objeto ultrapaddar este valor
     *
     * @var int
     */
    protected int $maxDepth = 512;

    /**
     * Propriedade para definir o número máximo de propriedades de cada objeto
     *
     * @var int
     */
    protected int $maxItems = 4096;

    /**
     * Propriedade para definir o número máximo de objetos na datasource
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
    public function setCheckInput(bool $check): Json|Xml
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
    public function setCheckOutput(bool $check): Json|Xml
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
     * Retorna valor da propriedade maxDepth
     *
     * @return  int
     */
    public function getMaxDepth()
    {
        return $this->maxDepth;
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
