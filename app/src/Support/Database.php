<?php

declare(strict_types=1);

namespace Api\DataMapper\Support;

use PDO;
use PDOException;

/**
 * Classe para conexão com o banco de dados
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
final class Database
{

    /**
     * Varipavel para armazenar a conexão
     * @var PDO|null
     */
    private static ?PDO $connection = null;

    /**
     * Recuperar conexão do banco de dados
     * 
     * @return PDO
     * @since 1.0.0
     */
    public static function getConnection(): ?PDO
    {
        if (self::$connection == null) {
            try {

                $dsn = "pgsql:host={$_ENV['POSTGRES_HOST']};port={$_ENV['POSTGRES_PORT']};dbname={$_ENV['POSTGRES_DATABASE']}";
                $pdo = new PDO(
                    $dsn,
                    $_ENV['POSTGRES_USER'],
                    $_ENV['POSTGRES_PASSWORD'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
                    ]
                );
                if ($pdo) {
                    self::$connection = $pdo;
                }
            } catch (PDOException $ex) {
                error_log(__CLASS__ . '->' . __FUNCTION__ . ": {$ex->getMessage()}");
                return null;
            }
        }
        return self::$connection;
    }
}
