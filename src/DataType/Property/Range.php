<?php

declare(strict_types=1);

namespace Inspire\DataMapper\DataType\Property;

/**
 * Classe base para propriedades de intervalo, como valor, tamanho e ocorrencias de um elemento.
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
class Range
{
    /**
     * Atributo para valor inicial ou mínimo
     *
     * @var int
     */
    protected int|float|null $beginning;

    /**
     * Atributo para valor final ou máximo
     *
     * @var int
     */
    protected int|float|null $end;

    /**
     * Construtor classe. Pode não receber os valores de propriedades na construção de objeto.
     *
     * @param int|float|null $beginning Valo rmínimo ou inicial
     * @param int|float|null $end Valor máximo ou final
     */
    public function __construct(int|float|null $beginning = null, int|float|null $end = null)
    {
        $this->beginning = $beginning;
        $this->end = $end;
    }

    /**
     * Retorna o valor minimo ou inial da propriedade.
     *
     * @return  int|float|null
     */
    public function getMinimum(): int|float|null
    {
        return $this->beginning;
    }

    /**
     * Retorna o valor máximo ou final da propriedade.
     *
     * @return  int|null|null
     */
    public function getMaximum(): int|float|null
    {
        return $this->end;
    }
}
