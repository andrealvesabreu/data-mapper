<?php

declare(strict_types=1);

namespace Inspire\DataMapper\DataType;

/**
 * Classe para elementos que contenham strings numéricas
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
final class Numeric extends Str
{

    /**
     * Propriedades aplicáveis a este tipo de dado
     */
    const PROPERTIES = [
        'element' => [
            'type' => 'string',
            'required' => true
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
            'type' => 'string',
            'default' => null
        ],
        'required' => [
            'type' => 'bool'
        ],
        'nullable' => [
            'type' => 'bool'
        ],
        'format' => [
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 100,
            'default' => ''
        ]
    ];

    /**
     * String com formato
     *
     * @var string|null
     */
    protected ?string $format = null;

    /**
     * Construtor da classe. Sempre receberá os dados para definir os valores de suas propriedades
     *
     * @param array $props Array com valores de propriedades
     * @param bool $fromSource Indica se os dados estão vindo de uma fonte de dados ou se recebeu os dados estraidos de uma fonte anteriormente
     */
    public function __construct(array $props, bool $fromSource = true)
    {
        if (isset($props['format'])) {
            $this->format = $props['format'];
        }
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
            //Setar valores primitivos e formatado. Será considerado valor primitivo o valor obtido apóes remoção da mascara do valor de entrada
            $this->value->setValues(
                $this->unmask((string)$props['value']),
                (string)$props['value']
            );
        }
    }
}
