<?php

declare(strict_types=1);

namespace Api\DataMapper\Element;

use Inspire\DataMapper\Mappers\{
    Base,
    Setup\Main
};

/**
 * Classe com funções para validação de elementos (type) antes de inserção no banco de dados
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
final class Type extends Base
{

    /**
     * Definição de função para mapeamento e extraão de dados do datasource
     * Apenas implementação de método estático da classe Base. Sem uso aqui.
     *
     * @param array $source Dados de entrada
     * @param bool $check Indicador apra validar ou não dados durante a extração
     *
     * @return void
     * @since 1.0.0
     */
    public function map(array $source, bool $check = true): void {}

    /**
     * Define função para compilação de dados para formato de destino
     * Apenas implementação de método estático da classe Base. Sem uso aqui.
     *
     * @param Base $source Objeto de tipo Base cque já tenha dados de extraão prévia
     *
     * @return void
     * @since 1.0.0
     */
    public function build(Base $source): void {}


    /**
     * Definição de função para retornar configuração
     * Apenas implementação de método estático da classe Base. Sem uso aqui.
     *
     * @return Main
     * @since 1.0.0
     */
    public function getSetup(): Main
    {
        return new Main([]);
    }

    /**
     * Validar definição de elemento
     * 
     * @param array $parsers lista de parsers onde se pretende que o elemtno se aplicável
     * @param array $definition dados de definição das propriedadse do elemento
     * 
     * @return array|null
     * @since 1.0.0
     */
    public static function checkFieldDefinition(array $parsers, array $definition): ?array
    {
        if (!isset($definition['type'])) {
            return [
                'status' => 0,
                'message' => "Falta o campo 'type' no grupo 'definition'"
            ];
        }
        $type = $definition['type'];
        unset($definition['type']);
        foreach ($parsers as $parser) {
            $class = ucfirst(strtolower($parser));
            $properties = [];
            switch ($class) {
                case 'Csv':
                    $properties = \Inspire\DataMapper\Mappers\Setup\Csv::PROPERTIES['type'];
                    break;
                case 'Csv':
                    $properties = \Inspire\DataMapper\Mappers\Setup\Csv::PROPERTIES['type'];
                    break;
                case 'Collection':
                    $properties = \Inspire\DataMapper\Mappers\Setup\Collection::PROPERTIES['type'];
                    break;
                case 'Json':
                    $properties = \Inspire\DataMapper\Mappers\Setup\Json::PROPERTIES['type'];
                    break;
                case 'Proceda':
                    $properties = \Inspire\DataMapper\Mappers\Setup\Proceda::PROPERTIES['type'];
                    break;
                case 'Xml':
                    $properties = \Inspire\DataMapper\Mappers\Setup\Xml::PROPERTIES['type'];
                    break;
            }
            if (isset($properties[$type])) {
                $def = $definition;;
                if ($class != 'Proceda') {
                    unset($def['start']);
                }
                $setup = (new self)->checkField(
                    $properties[$type],
                    $def
                );
                if ($setup['status'] == 0) {
                    $setup['detail'] = "Atributo obrigatório para o parser " . strtoupper($parser);
                    return $setup;
                }
            } else {
                return [
                    'status' => 0,
                    'message' => "Tipo de dado inválido: {$type}"
                ];
            }
        }
        return null;
    }
}
