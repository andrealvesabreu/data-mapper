<?php

declare(strict_types=1);

namespace Inspire\DataMapper\DataType;

use Inspire\DataMapper\Exceptions\ValueException;

/**
 * Classe para elementos que contenham strings
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
class Str extends Type implements TypeInterface
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
        ],
        'regex' => [
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 300,
            'default' => null
        ]
    ];

    /**
     * Date format
     * @var string
     */
    protected ?string $format = null;

    /**
     * Expressão regular
     * @var string|null|null
     */
    protected ?string $regex = null;

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
        if (isset($props['regex'])) {
            $this->regex = trim($props['regex'], '/');
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
                $props['value']
            );
        } //Bloco para definir dados com base em dados coletados de outro modelo anteriormente
        else {
            $masked = $props['value'];
            /**
             * Se for definido um formato, formatar a string.
             */
            if ($this->format !== null) {
                $masked = \Clemdesign\PhpMask\Mask::apply($props['value'], $this->format);
            }
            $this->value->setValues(
                $props['value'],
                $masked
            );
        }
    }

    /**
     * Remover mascara de uma string
     *
     * @param string|null $value A sttring formatada
     *
     * @return string|null
     */
    protected function unmask(?string $value): ?string
    {
        if (
            $this->format != null &&
            $value !== null
        ) {
            $unmasked = '';
            //Caracteres permitidos em mascara
            $charFormat = ['/', '(', ')', '.', ':', '-', '+', ',', '@', ' '];
            $originalValue = str_split($value);
            $pos = 0;
            foreach (str_split($this->format) as $token) {
                if (
                    isset($originalValue[$pos]) &&
                    (
                        !in_array($token, $charFormat) || // O caractere na máscara nesta posição não é um caractere de máscara válido
                        $originalValue[$pos] != $token //É um caractere de máscara valida, mas char na fonte nesta posição não é o mesmo
                    )
                ) {
                    $unmasked .= $originalValue[$pos];
                }
                $pos++;
            }
            return $unmasked;
        }
        return $value;
    }

    /**
     * Validar expressão regular
     * @return bool
     */
    public function validateRegex(): bool
    {
        if (!preg_match("/{$this->regex}/", $this->value->__toString())) {
            throw new ValueException("Constraint violation: The field '{$this->element}' does not match regex {$this->regex}.");
        }
        return true;
    }
}
