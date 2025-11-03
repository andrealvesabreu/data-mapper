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
 * Classe para configurações de parser do tipo Csv
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
final class Csv extends Main
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
            'header' => [
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
            'separator' => [
                'type' => 'string',
                'default' => ';',
                'minLength' => 0,
                'maxLength' => 100
            ],
            'enclosure' => [
                'type' => 'string',
                'default' => '"',
                'minLength' => 1,
                'maxLength' => 1
            ],
            'escape' => [
                'type' => 'string',
                'default' => '\\',
                'minLength' => 1,
                'maxLength' => 1
            ],
            'eol' => [
                'type' => 'string',
                'default' => "\n",
                'minLength' => 1,
                'maxLength' => 10
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

    //Constante que define um CSV com associação de cabeçalho de coluna
    const TYPE_ASSOC = 'assoc';
    //Constante que define um CSV baseado em posição de coluna (numérico)
    const TYPE_NUM = 'num';

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
     * Propriedade para definir o caracter delimitador de coluna/campo
     *
     * @var string
     */
    protected string $separator = ';';

    /**
     * Propriedade para definir o caracter de encapsulamento
     *
     * @var string
     */
    protected string $enclosure = '"';

    /**
     * Propriedade para definir o caracter de escape
     *
     * @var string
     */
    protected string $escape = '\\';

    /**
     * Propriedade para definir o caracter de quebra de linha
     *
     * @var string
     */
    protected string $eol = PHP_EOL;

    /**
     * Propriedade para definir o número máximo de propriedades de cada item (colunas) do CSV
     *
     * @var int
     */
    protected int $maxItems = 4096;

    /**
     * Propriedade para definir o número máximo de itens (linhas) do CSV
     *
     * @var int
     */
    protected int $maxRecords = 10000;

    /**
     * Propriedade para definir se o CSV de entrada conterá um cabeçalho ou não
     *
     * @var bool
     */
    protected bool $header = true;

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
    protected ?string $type = null;

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
    public function setCheckInput(bool $check): Csv
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
    public function setCheckOutput(bool $check): Csv
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
     * Retorna valor da propriedade de tipo (chaves associativas ou indexado numericamente)
     *
     * @return  string|null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Retorna valor caracter separador
     *
     * @return  string
     */
    public function getSeparator()
    {
        return $this->separator;
    }

    /**
     * Retorna valor de caracter de encapsulamento
     *
     * @return  string
     */
    public function getEnclosure()
    {
        return $this->enclosure;
    }

    /**
     * Retorna valor de caracter de escape
     *
     * @return  string
     */
    public function getEscape()
    {
        return $this->escape;
    }

    /**
     * Retorna valor de caracter de fim de linha
     *
     * @return  string
     */
    public function getEol()
    {
        return $this->eol;
    }

    /**
     * Função de validação de configuração
     *
     * @return bool
     */
    public function check(): bool
    {
        if (
            $this->type == Csv::TYPE_ASSOC &&
            $this->header == false
        ) {
            throw new SetupException("Header line is required when using ssociative datasource.");
        }
        return true;
    }

    /**
     * Retorna valor da propriedade header
     *
     * @return  bool
     */
    public function getHeader(): bool
    {
        return $this->header;
    }

    /**
     * Retorna valor da propriedade default
     *
     * @return  string
     */
    public function getDefault(): string
    {
        return $this->default;
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
     * Retorna o valor maximo de itens do CSV
     *
     * @return  int
     */
    public function getMaxRecords()
    {
        return $this->maxRecords;
    }
}
