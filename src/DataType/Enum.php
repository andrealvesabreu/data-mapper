<?php

declare(strict_types=1);

namespace Inspire\DataMapper\DataType;

use Inspire\DataMapper\Exceptions\ValueException;

/**
 * Classe para elementos que contenham Enum (listagem de valores permitidos)
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
final class Enum extends Type
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
        'enum' => [
            'type' => 'array',
            'minLength' => 0,
            'maxLength' => 100,
            'default' => ''
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
        $this->value->setValues(
            $props['value'],
            (string)$props['value']
        );
    }

    /**
     * Validar se o valor informado é permitido
     *
     * @return bool
     */
    public function isAllowedValue(): bool
    {
        if (
            $this->value !== null &&
            $this->value->getAllowedValues() !== null &&
            $this->value->getValue() !== null
        ) {
            /**
             * Restrição de valores permitidos
            */
            if (!in_array($this->value->__toString(), $this->value->getAllowedValues())) {
                throw new ValueException("Constraint violation: The field '{$this->element}' must be one of: " . implode(',', $this->value->getAllowedValues()));
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
