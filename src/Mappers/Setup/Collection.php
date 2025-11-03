<?php

declare(strict_types=1);

namespace Inspire\DataMapper\Mappers\Setup;

use Inspire\DataMapper\DataType\{
    Datetime,
    Decimal,
    Enum,
    Integer,
    Numeric,
    Str
};

/**
 * Classe para configurações de parser do tipo Collection
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @example examples/setup/collection.php How to use this function
 * @since 1.0.0
 */
final class Collection extends Main
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
                'default' => 0,
                'minimum' => 0,
                'maximum' => 4096
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
            'type' => [
                'type' => 'choice',
                'options' => [
                    'assoc',
                    'num'
                ],
                'default' => 'assoc'
            ],
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
     * Propriedade para definir o número máximo de propriedades de cada item da colecction
     *
     * @var int
     */
    protected int $maxItems = 4096;

    /**
     * Propriedade para definir o número máximo de itens da colecction
     *
     * @var int
     */
    protected int $maxRecords = 10000;

    /**
     * Propriedade para definir um valor default a ser utilizado em campos que não tenham essa propriedade definida explicitamente
     *
     * @var string|null
     */
    protected ?string $default = null;

    /**
     * Propriedade para definir o tipo de colection (objetos indexados com chaves associativas ou indices numéricos)
     *
     * @var string|null
     */
    protected ?string $type = 'assoc';

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
     * @return Collection
     */
    public function setCheckInput(bool $check): Collection
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
     * @return Collection
     */
    public function setCheckOutput(bool $check): Collection
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
     * Retorna valor da propriedade type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Retorna valor da propriedade default
     *
     * @return string|null
     */
    public function getDefault(): ?string
    {
        return $this->default;
    }

    /**
     * Método de validação da configuração
     *
     * @return bool
     */
    public function check(): bool
    {
        return true;
    }

    /**
     * Retorna o valor maximo de propriedades por item
     *
     * @return  int
     */
    public function getMaxItems()
    {
        return $this->maxItems;
    }

    /**
     * Retorna o valor maximo de itens na collection
     *
     * @return  int
     */
    public function getMaxRecords()
    {
        return $this->maxRecords;
    }
}
