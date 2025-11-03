<?php

declare(strict_types=1);

namespace Inspire\DataMapper\DataType;

use Inspire\DataMapper\Exceptions\ValueException;

/**
 * Classe para elementos que contenham números inteiros
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
final class Integer extends Type implements TypeInterface
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
            'type' => 'int',
            'default' => null
        ],
        'maximum' => [
            'type' => 'int',
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
            'type' => 'int',
            'default' => null
        ],
        'required' => [
            'type' => 'bool'
        ],
        'nullable' => [
            'type' => 'bool'
        ],
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
        $primitive = null;
        //Se nenhum valor for definido, atribuir valor padrão. Caso não haja um valor padrão, considerar null
        if (
            !isset($props['value']) ||
            $props['value'] === null
        ) {
            $primitive = $props['value'] = $props['default'] ?? null;
        } //Se for informado um valor, forçar conversão para inteiro
        else if (strlen((string)$props['value']) > 0) {
            $primitive = intval($props['value']);
        }
        //Definir valores primitivo e formatado, preservando valor do datasource
        $this->value->setValues(
            (string)$primitive,
            (string)$props['value']
        );
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
            $this->value->getValue() !== null
        ) {
            /**
             * Verifica se o valor é igual ao maior ou menor inteiros reconhecidos pelo SO. 
             * Neste caso, será considerado um erro por provavelmente o valor for ajustado para os limites que a linguagem consegue trabalhar.
             */
            if (
                $this->value->getValue() == PHP_INT_MAX
            ) {
                throw new ValueException("Constraint violation: The value of the field '{$this->element}' is equals PHP_INT_MAX.");
            } else if (
                $this->value->getValue() == PHP_INT_MIN
            ) {
                throw new ValueException("Constraint violation: The value of the field '{$this->element}' is equals PHP_INT_MIN.");
            }
            //Se for definido um intevalo limite
            if ($this->range !== null) {
                /**
                 * Restrição de valor mínimo
                 */
                if (
                    $this->range->getMinimum() !== null &&
                    intval($this->value->getValue()) < $this->range->getMinimum()
                ) {
                    throw new ValueException("Constraint violation: The field '{$this->element}' contains a value less than a minimum value of {$this->range->getMinimum()}.");
                }
                /**
                 * Restrição de valor máximo
                 */
                else if (
                    $this->range->getMaximum() !== null &&
                    intval($this->value->getValue()) > $this->range->getMaximum()
                ) {
                    throw new ValueException("Constraint violation: The field '{$this->element}' contains a value greatest than a maximum value of {$this->range->getMaximum()}.");
                }
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
