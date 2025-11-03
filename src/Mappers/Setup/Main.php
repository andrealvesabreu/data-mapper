<?php

declare(strict_types=1);

namespace Inspire\DataMapper\Mappers\Setup;

/**
 * Classe base para configurações de parser
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
abstract class Main
{
    /**
     * Construtor recebe as propriedades definidas no modelo e atribui às propriedade da classe
     *
     * @param array $setup
     */
    public function __construct(array $setup)
    {
        foreach ($setup as $key => $val) {
            if (
                is_string($key) &&
                property_exists($this, $key) &&
                $val !== null
            ) {
                $this->{$key} = $val;
            }
        }
    }

    /**
     * Método para validação de configuração
     *
     * @return bool
     */
    abstract public function check(): bool;
}
