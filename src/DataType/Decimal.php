<?php

declare(strict_types=1);

namespace Inspire\DataMapper\DataType;

use Inspire\DataMapper\Exceptions\ValueException;

/**
 * Classe para elementos que contenham números decimais
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
final class Decimal extends Type implements TypeInterface
{

    /**
     * Propriedades aplicáveis a este tipo de dado
     */
    const PROPERTIES = [
        'element' => [
            'type' => 'string',
            'required' => true
        ],
        'minimum' => [
            'type' => 'float',
            'default' => null
        ],
        'maximum' => [
            'type' => 'float',
            'default' => null
        ],
        'minLength' => [
            'type' => 'int',
            'default' => null,
            'minimum' => 0,
            'maximum' => 4096
        ],
        'maxLength' => [
            'type' => 'int',
            'default' => null,
            'minimum' => 0,
            'maximum' => 4096
        ],
        'default' => [
            'type' => 'float',
            'default' => null
        ],
        'required' => [
            'type' => 'bool'
        ],
        'nullable' => [
            'type' => 'bool'
        ],
        'decimals' => [
            'type' => 'int',
            'default' => 2,
            'minimum' => 0,
            'maximum' => 6
        ],
        'dec_sep' => [
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 1,
            'default' => ','
        ],
        'tho_sep' => [
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 1,
            'default' => ''
        ]
    ];

    /**
     * Número de casas decimais
     *
     * @var int
     */
    protected int $decimals;

    /**
     * Caracter de separador decimal
     *
     * @var string
     */
    protected string $dec_sep;

    /**
     * Caracter de separador de milhar
     *
     * @var string
     */
    protected string $tho_sep;

    /**
     * Construtor da classe. Sempre receberá os dados para definir os valores de suas propriedades
     *
     * @param array $props Array com valores de propriedades
     * @param bool $fromSource Indica se os dados estão vindo de uma fonte de dados ou se recebeu os dados estraidos de uma fonte anteriormente
     */
    public function __construct(array $props, bool $fromSource = true)
    {
        //Classe pai tratará as propriedades padrão
        parent::__construct($props, $fromSource);

        /**
         * Bloco para sefinir valor com base em dados de datasource
         */
        if ($fromSource) {
            //Se nenhum valor for definido, atribuir valor padrão. Caso não haja um valor padrão, considerar null
            if (
                !isset($props['value']) ||
                $props['value'] === null
            ) {
                $props['value'] = $props['default'] ?? null;
            }

            //Verifica se foi definido  um separador decimal. Caso não, espera-se que o valor já contenha apenas números
            if (($props['dec_sep'] ?? null) === null) {
                //Remove todos os caracteres não numéricos, caso haja algum
                $unmasked = preg_replace("/[^0-9]/", '', (string)$props['value'] ?? '');
                /**
                 * Adiciona um ponto como separador decimal (padrão computacional).
                 * Este trataviva se aplica a forntes de dados como PROCEDA/TIVIT e CNAB
                 */
                if (isset($props['decimals']) && !empty($unmasked)) {
                    $unmasked = substr($unmasked, 0, strlen($unmasked) - $props['decimals']) . '.' . substr($unmasked, strlen($unmasked) - $props['decimals']);
                }
            } //Caso tenha definidio um separador  decimal, remove todos os caracteres não numéricos da string, exceto o separador
            else {
                $unmasked = preg_replace("/[^0-9{$props['dec_sep']}]/", '', (string)$props['value']);
                //Se o separador não for '.', altera para definir o valor em padrão computacional
                if ($props['dec_sep'] != '.') {
                    $unmasked = str_replace($props['dec_sep'], '.', $unmasked);
                }
            }
            //Define valores primitivo (Sem formatação), e formatado com separador de '.' e 10 decimais (está será a precisão máxima de campos decimais)
            $this->value->setValues(
                $unmasked,
                number_format(floatval($unmasked), intval($props['decimals'] ?? 10), '.', '')
            );
        } //Bloco para definir dados com base em dados coletados de outro modelo anteriormente
        else {
            //Remover todos os caracterse não numéricos
            $unmasked = preg_replace("/[^0-9.]/", '', (string)$props['value'] ?? '');
            //Formatar com base nos valores da propriedades definidas
            $formatted = number_format(
                floatval($unmasked),
                $props['decimals'] ?? 0,
                $props['dec_sep'] ?? '',
                $props['tho_sep'] ?? ''
            );
            //Define valores primitivo (Sem formatação), e formatado
            $this->value->setValues(
                $unmasked,
                $formatted
            );
        }
    }

    /**
     * Valida se o valor informado está no intervalo delimitado, quando houver delimitação
     *
     * @return bool
     */
    public function validateRange(): bool
    {
        if (
            $this->value !== null &&
            $this->range !== null &&
            $this->value->getValue() !== null
        ) {
            /**
             * Restrição de valor mínimo
             */
            if (
                $this->range->getMinimum() !== null &&
                floatval($this->value->getValue()) < $this->range->getMinimum()
            ) {
                throw new ValueException("Constraint violation: The field '{$this->element}' contains a value less than a minimum value of {$this->range->getMinimum()}.");
            }
            /**
             * Restrição de valor máximo
             */
            else if (
                $this->range->getMaximum() !== null &&
                floatval($this->value->getValue()) > $this->range->getMaximum()
            ) {
                throw new ValueException("Constraint violation: The field '{$this->element}' contains a value greatest than a maximum value of {$this->range->getMaximum()}.");
            }
        } else if (!$this->nullable) {
            /**
             * Restrição de valor nulo
             */
            throw new ValueException("Constraint violation: Non nullable field '{$this->element}' has a null value.");
        }
        return true;
    }
}
