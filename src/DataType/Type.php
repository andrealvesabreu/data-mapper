<?php

declare(strict_types=1);

namespace Inspire\DataMapper\DataType;

use Exception;
use Inspire\DataMapper\DataType\Property\{
    Alignment,
    Length,
    Occurrences,
    Position,
    Value,
    Range
};
use Inspire\DataMapper\Exceptions\ValueException;
use Inspire\DataMapper\Mappers\Base;

/**
 * Classe base para todas as classes de tipo
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
abstract class Type
{
    /**
     * Constantes de tipagem para relacionar com o schema
     */
    const TYPE_DATETIME = 'datetime';
    const TYPE_DECIMAL = 'decimal';
    const TYPE_ENUM = 'enum';
    const TYPE_INT = 'integer';
    const TYPE_STR = 'string';
    const TYPE_NUMERIC = 'numeric';
    const TYPE_BOOLEAN = 'boolean';

    /**
     * Propriedade para delimitar tamanho de campo/elemento
     *
     * @var Length|null
     */
    protected ?Length $size = null;

    /**
     * Propriedade para delimitar o número de ocorrencias de um campo/elemento
     *
     * @var Occurrences|null
     */
    protected ?Occurrences $occur = null;

    /**
     * Propriedade para delimitar o intervalo de valores aceitos em um campo/elemento
     *
     * @var Range|null
     */
    protected ?Range $range = null;

    /**
     * Propriedade para alinhamento de valor no campo (aplicável à padrões com campo de tamanho fixo)
     *
     * @var Alignment|null
     */
    protected ?Alignment $align = null;

    /**
     * Propriedade para definir a posição de um elemento (aplicável à padrões de posição fixa)
     *
     * @var Position|null
     */
    protected ?Position $position = null;

    /**
     * Propriedade de valor do campo/elemento
     *
     * @var Value|null
     */
    protected ?Value $value = null;

    /**
     * Definir se um campo é obrigatório
     *
     * @var bool
     */
    protected bool $required = false;

    /**
     * Definir se um campo é anulável
     *
     * @var bool
     */
    protected bool $nullable = true;

    /**
     * Caracter de preenchimento (aplicável à padrões com campo de tamanho fixo)
     *
     * @var string|null
     */
    protected ?string $filler = null;

    /**
     * Nome do elemento a ser usado para identificar o campo entre os modelos
     *
     * @var string|null
     */
    protected ?string $element = null;

    /**
     * Construtor da classe. Sempre receberá os dados para definir os valores de suas propriedades
     *
     * @param array $props Array com valores de propriedades
     * @param bool $fromSource Indica se os dados estão vindo de uma fonte de dados ou se recebeu os dados estraidos de uma fonte anteriormente
     */
    public function __construct(array $props, bool $fromSource = true)
    {
        /**
         * Lista de valores permitidos, para tipos Enum
         */
        $enumList = null;
        if ($this instanceof Enum) {
            if (isset($props['enum']) && is_array($props['enum'])) {
                $enumList = $props['enum'];
            } else {
                $enumKey = str_replace('enums.', '', $props['enum'] ?? '');
                $enumList = Base::getRegisteredEnums($enumKey);
            }
        }

        $this->value = new Value(
            $props['default'] ?? null,
            $enumList
        );

        /**
         * Definir os limites minimo e maximo
         */
        if (isset($props['minimum']) || isset($props['maximum'])) {
            if ($this instanceof Decimal) {
                $this->range = new Range(
                    isset($props['minimum']) ? floatval($props['minimum']) : null,
                    isset($props['maximum']) ? floatval($props['maximum']) : null
                );
            } elseif ($this instanceof Datetime) {
                $this->range = new Range(
                    isset($props['minimum']) ? $this->getDate($props['minimum']) : null,
                    isset($props['maximum']) ? $this->getDate($props['maximum']) : null
                );
            } else {
                $this->range = new Range(
                    isset($props['minimum']) ? intval($props['minimum']) : null,
                    isset($props['maximum']) ? intval($props['maximum']) : null
                );
            }
        }

        /**
         * Definir o tamnhho minimo e máximo do campo
         */
        if (isset($props['minLength']) || isset($props['maxLength'])) {
            $this->size = new Length(
                isset($props['minLength']) ? intval($props['minLength']) : null,
                isset($props['maxLength']) ? intval($props['maxLength']) : null
            );
        }

        /**
         * Definir posição
         */
        if (isset($props['start']) || isset($props['end'])) {
            $this->position = new Position(
                isset($props['start']) ? intval($props['start']) : null,
                isset($props['end']) ? intval($props['end']) : null
            );
        }

        /**
         * Definir o alinhamento
         */
        if (isset($props['align'])) {
            $this->align = new Alignment($props['align']);
        }

        //Atribuir propriedades primitivas
        foreach (
            array_intersect(
                [
                    'required',
                    'nullable',
                    'element',
                    'filler',
                ],
                array_keys($props)
            ) as $key
        ) {
            $this->$key = $props[$key];
        }
    }

    /**
     * Criar o objeto Type em um tipo específico e retorná-lo
     *
     * @return Type|null
     */
    public static function create(array $props, bool $fromSource = true): ?Type
    {
        switch ($props['type'] ?? '') {
            case self::TYPE_DATETIME:
                $type = new Datetime($props, $fromSource);
                break;
            case self::TYPE_DECIMAL:
                $type = new Decimal($props, $fromSource);
                break;
            case self::TYPE_ENUM:
                $type = new Enum($props, $fromSource);
                break;
            case self::TYPE_INT:
                $type = new Integer($props, $fromSource);
                break;
            case self::TYPE_STR:
                $type = new Str($props, $fromSource);
                break;
            case self::TYPE_NUMERIC:
                $type = new Numeric($props, $fromSource);
                break;
            case self::TYPE_BOOLEAN:
                $type = new Boolean($props, $fromSource);
                break;
            default:
                throw new Exception("Unknown data type {$props['type']}.");
                return null;
        }
        return $type;
    }

    /**
     * Retornar o nome do elemento
     *
     * @return  string|null
     */
    public function getElement(): ?string
    {
        return $this->element;
    }

    /**
     * Renomeia o elemento
     * @param string $element
     * 
     * @return void
     */
    public function setElement(string $element): void
    {
        $this->element = $element;
    }

    /**
     * Retornar o objeto value do elemento
     *
     * @return  Value|null
     */
    public function getValue(): ?Value
    {
        return $this->value;
    }

    /**
     * Retornar o objeto range do elemento
     *
     * @return  Range
     */
    public function getRange(): ?Range
    {
        return $this->range;
    }

    /**
     * Retornar o objeto position do elemento
     *
     * @return  Position
     */
    public function getPosition(): ?Position
    {
        return $this->position;
    }

    /**
     * Retornar a propriedade de alinhamento do elemento, qe poderá ser STR_PAD_BOTH, STR_PAD_LEFT ou STR_PAD_RIGHT
     *
     * @return  int|null
     */
    public function getAlignment(): ?int
    {
        if ($this->align) {
            return $this->align->getAlign();
        }
        return null;
    }

    /**
     * Retornar a propriedade de preenchimento do elemento
     *
     * @return  string|null
     */
    public function getFiller(): ?string
    {
        return $this->filler;
    }

    /**
     * Retornar o objeto size do elemento
     *
     * @return  Length|null
     */
    public function getSize(): ?Length
    {
        return $this->size;
    }

    /**
     * Validar restrição de tamnho de campo
     *
     * @return bool
     */
    public function validateSize(): bool
    {
        if (
            $this->value !== null &&
            $this->size !== null &&
            $this->value->getValue() !== null
        ) {
            /**
             * Restrição de tamano mínimo
             */
            if (
                $this->size->getMinimum() !== null &&
                mb_strlen($this->value->__toString()) < $this->size->getMinimum()
            ) {
                throw new ValueException("Constraint violation: The field '{$this->element}' a value less than a minimum size of {$this->size->getMinimum()}.");
            }
            /**
             * Restrição de tamanho mázimo
             */
            elseif (
                $this->size->getMaximum() !== null &&
                mb_strlen($this->value->__toString()) > $this->size->getMaximum()
            ) {
                throw new ValueException("Constraint violation: The field '{$this->element}' a value greatest than a maximum size of {$this->size->getMaximum()}.");
            }
        } elseif (!$this->nullable) {
            /**
             * Restrição de valor nulo
             */
            throw new ValueException("Constraint violation: Non nullable field '{$this->element}' has a null value.");
        }
        return true;
    }

    /**
     * Método padrão para validação de intervalo de valor. Retornará sempre true, devendo ser sobrescrito em tipos em que a validação seja aplicável
     *
     * @return bool
     */
    public function validateRange(): bool
    {
        return true;
    }

    /**
     * Método padrão para validação valor permitido. Retornará sempre true, devendo ser sobrescrito em tipos em que a validação seja aplicável
     *
     * @return bool
     */
    public function isAllowedValue(): bool
    {
        return true;
    }

    /**
     * Método padrão para validação de expressão regular. Retornará sempre true, devendo ser sobrescrito em tipos em que a validação seja aplicável
     *
     * @return bool
     */
    public function validateRegex(): bool
    {
        return true;
    }

    /**
     * Simplificação para executar validação completa em uma função.
     *
     * @return bool
     */
    public function validate(bool $isProceda = false): bool
    {
        return $this->validateRange() &&
            (!$isProceda  || $this->validateSize()) &&
            $this->isAllowedValue() &&
            $this->validateRegex();
    }

    /**
     * Verificar se o element é obrigatório
     *
     * @return  bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }
}
