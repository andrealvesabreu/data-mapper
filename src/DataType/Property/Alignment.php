<?php

declare(strict_types=1);

namespace Inspire\DataMapper\DataType\Property;

/**
 * Classe para propriedade de alinhamento dos elementos
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
final class Alignment
{
    const ALIGN_CENTER = 'center';
    const ALIGN_LEFT = 'left';
    const ALIGN_RIGHT = 'right';

    /**
     * Alinhamento do elemento. Por padrão, será à esquerda
     *
     * @var string
     */
    private string $align = Alignment::ALIGN_LEFT;

    /**
     * Construtor da classe. Obrigatóriamente receberá um valor para alinhamento
     *
     * @param string $align
     */
    public function __construct(string $align)
    {
        $this->setAlign($align);
    }

    /**
     * Obtem o valor de alinhamento
     * Se o valor de alinhamento não for um valor válido, retornará o valor constante STR_PAD_LEFT
     *
     * @return  int
     */
    public function getAlign(): int
    {
        switch ($this->align) {
            case Alignment::ALIGN_CENTER:
                return STR_PAD_BOTH;
            case Alignment::ALIGN_LEFT:
                return STR_PAD_LEFT;
            case Alignment::ALIGN_RIGHT:
                return STR_PAD_RIGHT;
        }
        return STR_PAD_LEFT;
    }

    /**
     * Define o valor de alinhamento.
     * Se o valor informado não for válido, o valor atual não será alterado.
     *
     * @param  string $align O valor se alinhamento para atribuir ao elemento
     *
     * @return  Alignment
     */
    public function setAlign(string $align): Alignment
    {
        if (in_array($align, [
            Alignment::ALIGN_CENTER,
            Alignment::ALIGN_LEFT,
            Alignment::ALIGN_RIGHT
        ])) {
            $this->align = $align;
        }
        return $this;
    }
}
