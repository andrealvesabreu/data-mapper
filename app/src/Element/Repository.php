<?php

declare(strict_types=1);

namespace Api\DataMapper\Element;

use Api\DataMapper\Support\Database;
use Illuminate\Support\Arr;
use Exception;
use PDO;
use PDOException;

/**
 * Classe de repositório para gravação e consulta em banco de dados
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
final class Repository
{
    /**
     * Objeto PDO para conexão com o banco de dados
     * @var PDO|null|null
     */
    private ?PDO $connection = null;

    /**
     * Construtor da classe.
     * Estabelecer a conexão com o banco de dados, pois todos os métodos da classe precisaram dela
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->connection = Database::getConnection();
    }

    /**
     * Método para consulta de elementos cadastrados
     * @param string $namespace nome de namespace para consulta
     * @param string|null $element nome de elemento para consulta
     * 
     * @return array|null
     * @since 1.0.0
     */
    public function get(string $namespace, ?string $element = null): ?array
    {
        if ($element !== null) {
            $stmt = $this->connection->prepare('SELECT nome, namespaces, tipos, definicao 
                                        FROM elementos
                                        WHERE (namespaces::jsonb)  @> ? and nome = ?');
            $stmt->bindValue(1, json_encode([$namespace]), PDO::PARAM_STR);
            $stmt->bindValue(2, $element, PDO::PARAM_STR);
        } else {
            $stmt = $this->connection->prepare('SELECT nome, namespaces, tipos, definicao 
                                        FROM elementos
                                        WHERE (namespaces::jsonb)  @> ?');
            $stmt->bindValue(1, json_encode([$namespace]), PDO::PARAM_STR);
        }
        $stmt->execute();
        $elements = [];
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch()) {
                $elements["{$namespace}:{$row->nome}"] = json_decode($row->definicao, true);
            }
            return $elements;
        }
        return null;
    }

    /**
     * Método para exclusão de um elemento da base de dados
     * @param string $namespace nome de namespace para exclusão
     * @param string $element nome de elemento para exclusão
     * 
     * @return bool
     * @since 1.0.0
     */
    public function delete(string $namespace, string $element): bool
    {
        $stmt = $this->connection->prepare('SELECT id, namespaces
                                        FROM elementos
                                        WHERE (namespaces::jsonb)  @> ? and nome = ?');
        $stmt->bindValue(1, json_encode([$namespace]), PDO::PARAM_STR);
        $stmt->bindValue(2, $element, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch()) {
                $decode = json_decode($row->namespaces, true);
                if (count($decode) == 1) {
                    $this->connection->query("DELETE FROM elementos WHERE id = {$row->id}");
                } else {
                    $decode = json_encode(
                        array_diff(
                            $decode,
                            [$namespace]
                        )
                    );
                    $this->connection->query("UPDATE elementos SET namespaces = '{$decode}' WHERE id = {$row->id}");
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Método para consulta um elemento por ID
     * @param int $id id do elemento a ser consultado
     * 
     * @return array|null
     * @since 1.0.0
     */
    public function getId(int $id): ?array
    {
        $stmt = $this->connection->prepare('SELECT nome, namespaces, tipos, definicao 
                                        FROM elementos
                                        WHERE id = ?');
        $stmt->bindValue(1, $id, PDO::PARAM_INT);
        $stmt->execute();
        $elements = [];
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch()) {
                foreach (json_decode($row->namespaces, true) as $namespace) {
                    $elements["{$namespace}:{$row->nome}"] = json_decode($row->definicao, true);
                }
            }
            return $elements;
        }
        return null;
    }

    /**
     * Método para cadastrar um elemnto
     * @param array $data definição de dados do elemento
     * 
     * @return int
     * @since 1.0.0
     */
    public function post(array $data): int
    {
        try {
            $element = Arr::get($data, 'definition.element');
            $namespaces = Arr::get($data, 'namespaces');
            /**
             * Verifica se já existe um elemento com mesmo nome e namespace
             */
            foreach ($namespaces as $namespace) {
                $stmt = $this->connection->prepare('SELECT id, namespaces
                                        FROM elementos
                                        WHERE (namespaces::jsonb)  @> ? and nome = ?');
                $stmt->bindValue(1, json_encode([$namespace]), PDO::PARAM_STR);
                $stmt->bindValue(2, $element, PDO::PARAM_STR);
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    throw new Exception("Ja existe um elemento '{$element}' cadastrado no namespace '{$namespace}'");
                }
            }
            Arr::forget($data, 'definition.element');
            $this->connection->beginTransaction();
            $stmt = $this->connection->prepare('INSERT INTO  elementos (nome, namespaces, tipos, definicao)
                                            VALUES(?, ?, ?, ?)');
            $stmt->bindValue(1, $element, PDO::PARAM_STR);
            $stmt->bindValue(2, json_encode($namespaces), PDO::PARAM_STR);
            $stmt->bindValue(3, json_encode(Arr::get($data, 'types')), PDO::PARAM_STR);
            $stmt->bindValue(4, json_encode(Arr::get($data, 'definition')), PDO::PARAM_STR);
            $stmt->execute();
            $this->connection->commit();
            return intval($this->connection->lastInsertId());
        } catch (PDOException $e) {
            //Reverter transação em caso de erros
            if ($this->connection->inTransaction()) {
                $this->connection->rollback();
            }
            return 0;
        }
    }
}
