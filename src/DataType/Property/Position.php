<?php

declare(strict_types=1);

namespace Inspire\DataMapper\DataType\Property;

/**
 * Classe para propriedade de posição dos elementos, aplicável apenas a formatos baseados em posição como PROCEDA/TIVIT e CNAB
 * A propriedade comprimento neste tipo de elemento deverá ser invarivavelmente a diferença entre as posições inicial e final deste onjeto.
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
final class Position extends Range
{

    /**
     * Retorna a posição inicial do elemento
     *
     * @return  int|float|null
     */
    public function getStart(): int|float|null
    {
        return $this->beginning;
    }

    /**
     * Retorna a posição final do elemento.
     *
     * @return  int|null|null
     */
    public function getEnd(): int|float|null
    {
        return $this->end;
    }
}
