<?php

declare(strict_types=1);

namespace Inspire\DataMapper\Mappers;

/**
 * Interface para definição de métodos obrigatórios de classes de extração
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
interface MapperInterface
{

    /**
     * Método para extrair dados do datasource
     *
     * @param array $source Dados de entrada
     * @param bool $check indicador para validar ou não dados durante a leitura
     *
     * @return void
     */
    public function map(array $source, bool $check = true): void;

    /**
     * Compila dados para formato de destino
     *
     * @param Base $source Objeto do tipo Base com dados extraidos previamente de modelo de origem 
     *
     * @return void
     */
    public function build(Base $source): void;
}
