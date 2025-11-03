<?php

declare(strict_types=1);

namespace Api\DataMapper\Model;

use Api\DataMapper\Support\Database;
use Exception;
use Illuminate\Support\Arr;
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
     * Método para consulta de modelos cadastrados
     * @param string $name nome de modelo para consulta
     * @param string|null $parser nome do parser para consulta
     * 
     * @return array|null
     * @since 1.0.0
     */
    public function get(string $name, ?string $parser = null): ?array
    {
        if ($parser !== null) {
            $stmt = $this->connection->prepare('SELECT nome, parser, definicao 
                                        FROM modelos
                                        WHERE nome = ? and parser = ?');
            $stmt->bindValue(1, $name, PDO::PARAM_STR);
            $stmt->bindValue(2, $parser, PDO::PARAM_STR);
        } else {
            $stmt = $this->connection->prepare('SELECT nome, parser, definicao 
                                        FROM modelos
                                        WHERE nome = ?');
            $stmt->bindValue(1, $name, PDO::PARAM_STR);
        }
        $stmt->execute();
        $elements = [];
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch()) {
                $elements["{$name}:{$row->parser}"] = json_decode($row->definicao, true);
            }
            return $elements;
        }
        return null;
    }

    /**
     * Método para exclusão de um modelos da base de dados
     * @param string $name nome de modelo para exclusão
     * @param string $parser nome de parser para exclusão
     * 
     * @return bool
     * @since 1.0.0
     */
    public function delete(string $name, ?string $parser = null): bool
    {
        if ($parser !== null) {
            $stmt = $this->connection->prepare('SELECT id
                                        FROM modelos
                                        WHERE nome = ? and parser = ?');
            $stmt->bindValue(1, $name, PDO::PARAM_STR);
            $stmt->bindValue(2, $parser, PDO::PARAM_STR);
        } else {
            $stmt = $this->connection->prepare('SELECT id 
                                        FROM modelos
                                        WHERE nome = ?');
            $stmt->bindValue(1, $name, PDO::PARAM_STR);
        }
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch()) {
                $this->connection->query("DELETE FROM modelos WHERE id = {$row->id}");
            }
            return true;
        }
        return false;
    }

    /**
     * Método para cadastrar um modelo
     * @param array $data definição de dados do modelo
     * 
     * @return int
     * @since 1.0.0
     */
    public function post(array $data): int
    {
        try {
            $name = Arr::get($data, 'name');
            $parser = Arr::get($data, 'parser');
            /**
             * Verifica se ja existe um modelo com este nome
             */
            $stmt = $this->connection->prepare('SELECT id
                                        FROM modelos
                                        WHERE parser = ? and nome = ?');
            $stmt->bindValue(1, $parser, PDO::PARAM_STR);
            $stmt->bindValue(2, $name, PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                throw new Exception("Ja existe um modelo chamado '{$name}' cadastrado utilizando o parser '{$parser}'");
            }

            $this->connection->beginTransaction();
            $stmt = $this->connection->prepare('INSERT INTO  modelos (nome, parser, definicao)
                                            VALUES(?, ?, ?)');
            $stmt->bindValue(1, $name, PDO::PARAM_STR);
            $stmt->bindValue(2, $parser, PDO::PARAM_STR);
            $stmt->bindValue(3, json_encode($data), PDO::PARAM_STR);
            $stmt->execute();
            $this->connection->commit();
            return intval($this->connection->lastInsertId());
        } catch (PDOException $e) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollback();
            }
            return 0;
        }
    }
}
