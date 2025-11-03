<?php

declare(strict_types=1);

namespace Inspire\DataMapper\DataType\Property;

/**
 * Classe para propriedade de valor de um elemento.
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
class Value
{

    /**
     * Valor padrão para o elemento
     *
     * @var string|null
     */
    private ?string $default;

    /**
     * Dados como formato primitivo (int, float, string sem mascara)
     *
     * @var string|null
     */
    private ?string $primitive;

    /**
     * Valor formatado
     *
     * @var string|null
     */
    private ?string $formatted;

    /**
     * Valores permitidos para o elemento, quando este for do tipo Enum
     *
     * @var array|null
     */
    private ?array $allowed;

    /**
     * Construtor da classe
     *
     * @param mixed|null $default
     * @param array|null $allowed
     */
    public function __construct(mixed $default = null, ?array $allowed = [])
    {
        $this->default = (string)$default;
        $this->allowed = $allowed;
    }

    /**
     * Atualiza valores primitivo e formatado
     *
     * @param null $primitive Valor como tipo primitivo
     * @param string|null $formatted Valor como string formatada
     *
     * @return Value
     */
    public function setValues($primitive = null, ?string $formatted = null): Value
    {
        $this->primitive = null;
        if ($primitive !== null) {
            $this->primitive = (string) $primitive;
        }
        $this->formatted = null;
        if ($formatted !== null) {
            $this->formatted = (string) $formatted;
        }
        return $this;
    }

    /**
     * Devolve o valor primitivo
     *
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->primitive;
    }

    /**
     * Devolve valor como string formatada
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string) ($this->formatted ?? $this->primitive ?? $this->default);
    }

    /**
     * Devolve lista de valores permitidos, para elementos do tipo Enum
     *
     * @return array|null
     */
    public function getAllowedValues(): ?array
    {
        return $this->allowed;
    }
}
