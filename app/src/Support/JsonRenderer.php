<?php

declare(strict_types=1);

namespace Api\DataMapper\Support;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Classe para retorno de requests HTTP em formato JSON
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
final class JsonRenderer
{

    /**
     * Método para compilar respostas HTTP em formato JSON
     * @param string $message
     * @param int $code
     * @param bool|null $status
     * @param array|null $data
     * 
     * @return ResponseInterface
     */
    public static function json(
        string $message,
        int $code = 200,
        ?bool $status = null,
        ?array $data = null
    ): ResponseInterface {
        $response = (new Response())
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withStatus($code);
        $response->getBody()
            ->write(
                json_encode(
                    compact('message', 'status', 'data')
                )
            );
        return $response;
    }
}
