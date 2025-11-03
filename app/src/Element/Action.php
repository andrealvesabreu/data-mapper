<?php

declare(strict_types=1);

namespace Api\DataMapper\Element;

use Api\DataMapper\Support\JsonRenderer;
use Slim\Psr7\{
    Request,
    Response
};
use Psr\Http\Message\ResponseInterface;

/**
 * Action para cadastro de elementos
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
final class Action
{
    /**
     * Objeto de Element/Repository
     * @var Repository
     */
    private Repository $elementRepository;

    /**
     * Construtor da classe. Injetar dependencia da classe repository
     * @param Repository $elementRepository
     * @since 1.0.0
     */
    public function __construct(Repository $elementRepository)
    {
        $this->elementRepository = $elementRepository;
    }

    /**
     * Método para consulta de elementos cadastrados
     * @param Request $request
     * @param Response $response
     * @param array $args
     * 
     * @return ResponseInterface
     */
    public function get(Request $request, Response $response, array $args): ResponseInterface
    {
        $elements = $this->elementRepository
            ->get($args['namespace'], $args['element'] ?? null);
        if (
            is_array($elements) &&
            !empty($elements)
        ) {
            return JsonRenderer::json(
                'OK',
                200,
                true,
                $elements
            );
        }
        return JsonRenderer::json(
            'No data found',
            404,
            false
        );
    }

    /**
     * Método para criação/alteração de elementos
     * @param Request $request
     * 
     * @return ResponseInterface
     */
    public function post(Request $request): ResponseInterface
    {
        try {
            $data = json_decode($request->getBody()->__toString(), true);
            /**
             * Valida os dados do pacote para verificar se atende as especificações necessárias para cada parser indicado
             */
            $errors = Type::checkFieldDefinition($data['types'], $data['definition']);
            if ($errors !== null) {
                return JsonRenderer::json(
                    'Error',
                    400,
                    false,
                    $errors
                );
            }
            /**
             * Insere elemento no banco
             */
            $id = $this->elementRepository
                ->post(json_decode($request->getBody()->__toString(), true));
            if ($id > 0) {
                $element = $this->elementRepository
                    ->getId($id);
                return JsonRenderer::json(
                    'Created',
                    201,
                    true,
                    [$element]
                );
            }
            return JsonRenderer::json(
                'Error',
                500,
                false
            );
        } catch (\Exception $e) {
            return JsonRenderer::json(
                $e->getMessage(),
                400,
                false
            );
        }
    }

    /**
     * Método para excluir elementos
     * @param Request $request
     * @param Response $response
     * @param array $args
     * 
     * @return ResponseInterface
     */
    public function delete(Request $request, Response $response, array $args): ResponseInterface
    {
        if (
            $this->elementRepository
            ->delete($args['namespace'], $args['element'])
        ) {
            return JsonRenderer::json(
                'Deleted',
                200,
                true
            );
        }
        return JsonRenderer::json(
            'No data found',
            404,
            false
        );
    }
}
