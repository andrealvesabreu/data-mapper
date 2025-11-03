<?php

declare(strict_types=1);

namespace Inspire\DataMapper\DataType;

use Inspire\DataMapper\DataType\Property\BoolValue;

/**
 * Classe para elementos que contenham Enum (listagem de valores permitidos)
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
final class Boolean extends Type
{

    /**
     * Propriedades aplicáveis a este tipo de dado
     */
    const PROPERTIES = [
        'element' => [
            'type' => 'string',
            'required' => true
        ],
        'default' => [
            'type' => 'string',
            'default' => null
        ]
    ];

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
        //Se nenhum valor for definido, atribuir valor padrão. Caso não haja um valor padrão, considerar null
        if (
            !isset($props['value']) ||
            $props['value'] === null
        ) {
            $props['value'] = $props['default'] ?? null;
        }
        //Valores primitivo e formatado serão considerados o mesmo
        $this->value = new BoolValue($props['value'] ?? null);
        $this->value->setValues(
            $props['value'],
            null
        );
    }
}
