<?php

declare(strict_types=1);

namespace Inspire\DataMapper\DataType;

use Inspire\DataMapper\DataType\Property\{
    Length,
    Position,
    Range,
    Value
};

/**
 * Interface base para definição de métodos aplocáveis a todas as classe Type
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
interface TypeInterface
{
    /**
     * Métodos getters
     */
    public function getValue(): ?Value;
    public function getRange(): ?Range;
    public function getSize(): ?Length;
    public function getPosition(): ?Position;
    public function getAlignment(): ?int;

    /**
     * Métodos de validação
     */
    public function validateRange(): bool;
    public function validateSize(): bool;
    public function isAllowedValue(): bool;
}
