<?php

declare(strict_types=1);

namespace Api\DataMapper\Model;

use Api\DataMapper\Support\Database;
use Inspire\DataMapper\Mappers\Builder;

/**
 * Classe com funções para validação de modelos antes de inserção no banco de dados
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
final class Type
{

    /**
     * Estrutura de modelo após compilação das definições
     * @var array
     */
    private static array $builtModel = [];

    /**
     * Método para validar a estrutura do modelo
     * @param array $data estrutura de dados de definição do modelo
     * 
     * @return bool
     * @since 1.0.0
     */
    public static function checkDefinition(array $data): bool
    {
        $builder = new Builder();
        $pdo = Database::getConnection();
        /**
         * Carrega todas as definições de elementos do banco de dados
         */
        $stmt = $pdo->prepare('SELECT nome, namespaces, tipos, definicao 
                            FROM elementos');
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch()) {
                $builder->addElement($row->nome, json_decode($row->definicao, true), json_decode($row->namespaces, true));
            }
        }

        /**
         * Caso haja o grupo "types" no modelo, também é necessário compilar a definição deles
         */
        if (
            isset($data['types']) &&
            is_array($data['types']) &&
            !empty($data['types'])
        ) {
            foreach ($data['types'] as $name => &$type) {
                $builder->mergeSpecs($name, $type);
            }
        }
        /**
         * Compilar o modelo
         */
        self::$builtModel = $builder->build($data);
        /**
         * Validar estrutura
         */
        return $builder->checkSchema(self::$builtModel);
    }

    /**
     * Método para retornar o modelo compilado
     * @return array
     * @since 1.0.0
     */
    public static function getModel(): ?array
    {
        return self::$builtModel;
    }
}
