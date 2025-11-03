<?php

declare(strict_types=1);

namespace Api\DataMapper\Model;

use Api\DataMapper\Model\Repository;
use Api\DataMapper\Support\JsonRenderer;
use DOMDocument;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Inspire\DataMapper\Mappers\Base;
use Inspire\DataMapper\Mappers\Builder;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
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
     * Objeto de Model/Repository
     * @var Repository
     */
    private Repository $modelRepository;

    /**
     * Construtor da classe. Injetar dependencia da classe repository
     * @param Repository $elementRepository
     * @since 1.0.0
     */
    public function __construct(Repository $elementRepository)
    {
        $this->modelRepository = $elementRepository;
    }

    /**
     * Método para consulta de modelos cadastrados
     * @param Request $request
     * @param Response $response
     * @param array $args
     * 
     * @return ResponseInterface
     * @since 1.0.0
     */
    public function get(Request $request, Response $response, array $args): ResponseInterface
    {
        $elements = $this->modelRepository
            ->get($args['name'], $args['parser'] ?? null);
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
     * Método para criação/alteração de modelos
     * @param Request $request
     * 
     * @return ResponseInterface
     * @since 1.0.0
     */
    public function post(Request $request): ResponseInterface
    {
        try {
            $data = json_decode($request->getBody()->__toString(), true);
            /**
             * Check if there's all required fields in $args array
             */
            $missing = array_diff_key(
                array_flip([
                    'parser',
                    'name',
                    'setup',
                    'definitions'
                ]),
                $data
            );
            if (!empty($missing)) {
                return JsonRenderer::json(
                    'Erro nos dados de entrada. Você deve preencher todos os campos obrigatórios: ' . implode(', ', array_keys($missing)),
                    400,
                    false
                );
            }
            Type::checkDefinition($data);
            $id = $this->modelRepository
                ->post(json_decode($request->getBody()->__toString(), true));

            if ($id > 0) {
                return JsonRenderer::json(
                    'Created',
                    201,
                    true,
                    Type::getModel()
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
     * Método para compilação de modelos
     * @param Request $request
     * @param Response $response
     * @param array $args
     * 
     * @return ResponseInterface
     * @since 1.0.0
     */
    public function build(Request $request, Response $response, array $args): ResponseInterface
    {
        try {
            $models = $this->modelRepository
                ->get($args['name'], $args['parser']);
            if (
                is_array($models) &&
                !empty($models)
            ) {
                $data = array_shift($models);
                Type::checkDefinition($data);
                $args['name'] = str_replace(' ', '_', $args['name']);
                file_put_contents(
                    DIR_MODELS . "/{$args['parser']}_{$args['name']}.php",
                    "<?php\n\nreturn " . (new Builder())->varExportShort(Type::getModel()) . ';'
                );
                return JsonRenderer::json(
                    "Built model {$args['parser']}_{$args['name']}",
                    201,
                    true
                );
            }
            return JsonRenderer::json(
                'No data found',
                404,
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
     * Método para exclusão de modelos
     * @param Request $request
     * @param Response $response
     * @param array $args
     * 
     * @return ResponseInterface
     * @since 1.0.0
     */
    public function delete(Request $request, Response $response, array $args): ResponseInterface
    {
        if (
            $this->modelRepository
            ->delete($args['name'], $args['parser'] ?? null)
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

    /**
     * Método para extração de dados
     * @param Request $request
     * 
     * @return ResponseInterface
     * @since 1.0.0
     */
    public function extract(Request $request): ResponseInterface
    {
        try {
            $inputData = json_decode($request->getBody()->__toString(), true);
            /**
             * Check if there's all required fields in $args array
             */
            $missing = array_diff_key(
                array_flip([
                    'model',
                    'data'
                ]),
                $inputData
            );
            if (!empty($missing)) {
                return JsonRenderer::json(
                    'Erro nos dados de entrada. Você deve preencher todos os campos obrigatórios: ' . implode(', ', array_keys($missing)),
                    400,
                    false
                );
            }

            $model = include DIR_MODELS . "/{$inputData['model']}.php";
            $convertedData = Base::extractToArray(
                $model,
                $inputData['data'],
                null,
                function ($result) {
                    $client = new Client();
                    foreach ($result as &$res) {
                        try {
                            $cep = trim($res['EMIT_CEP']->getValue()->__toString());
                            $response = $client->request('GET', "https://viacep.com.br/ws/{$cep}/json/", [
                                'timeout'  => 5.0,
                            ]);
                            if ($response->getStatusCode() === 200) {
                                $body = $response->getBody()->getContents();
                                $data = json_decode($body, true);
                                $res['EMIT_SIAFI'] = \Inspire\DataMapper\DataType\Type::create([
                                    'value' => $data['siafi'],
                                    'type' => 'string',
                                    'element' => "EMIT_SIAFI"
                                ]);
                            }
                        } catch (RequestException $e) {
                        } catch (\Exception $e) {
                        }
                        try {
                            $cep = trim($res['DEST_CEP']->getValue()->__toString());
                            $response = $client->request('GET', "https://viacep.com.br/ws/{$cep}/json/", [
                                'timeout'  => 5.0,
                            ]);
                            if ($response->getStatusCode() === 200) {
                                $body = $response->getBody()->getContents();
                                $data = json_decode($body, true);
                                $res['DEST_SIAFI'] = \Inspire\DataMapper\DataType\Type::create([
                                    'value' => $data['siafi'] ?? '',
                                    'type' => 'string',
                                    'element' => "DEST_SIAFI"
                                ]);
                            }
                        } catch (RequestException $e) {
                        } catch (\Exception $e) {
                        }
                    }
                    return $result;
                }
            );
            return JsonRenderer::json(
                'Extracted',
                200,
                true,
                $convertedData
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
     * Método para converter dados entre modelos
     * @param Request $request
     * 
     * @return ResponseInterface
     * @since 1.0.0
     */
    public function convert(Request $request): ResponseInterface
    {
        try {
            $inputData = json_decode($request->getBody()->__toString(), true);
            /**
             * Check if there's all required fields in $args array
             */
            $missing = array_diff_key(
                array_flip([
                    'modelInput',
                    'modelOutput',
                    'data'
                ]),
                $inputData
            );
            if (!empty($missing)) {
                return JsonRenderer::json(
                    'Erro nos dados de entrada. Você deve preencher todos os campos obrigatórios: ' . implode(', ', array_keys($missing)),
                    400,
                    false
                );
            }

            $modelInput = include DIR_MODELS . "/{$inputData['modelInput']}.php";
            $modelOutput = include DIR_MODELS . "/{$inputData['modelOutput']}.php";
            $convertedData = Base::convert(
                $modelInput,
                $modelOutput,
                $inputData['data'],
                function ($result) {
                    $client = new Client();
                    foreach ($result as &$res) {
                        try {
                            $cep = trim($res['EMIT_CEP']->getValue()->__toString());
                            $response = $client->request('GET', "https://viacep.com.br/ws/{$cep}/json/", [
                                'timeout'  => 5.0,
                            ]);
                            if ($response->getStatusCode() === 200) {
                                $body = $response->getBody()->getContents();
                                $data = json_decode($body, true);
                                $res['EMIT_SIAFI'] = \Inspire\DataMapper\DataType\Type::create([
                                    'value' => $data['siafi'],
                                    'type' => 'string',
                                    'element' => "EMIT_SIAFI"
                                ]);
                            }
                        } catch (RequestException $e) {
                        } catch (\Exception $e) {
                        }
                        try {
                            $cep = trim($res['DEST_CEP']->getValue()->__toString());
                            $response = $client->request('GET', "https://viacep.com.br/ws/{$cep}/json/", [
                                'timeout'  => 5.0,
                            ]);
                            if ($response->getStatusCode() === 200) {
                                $body = $response->getBody()->getContents();
                                $data = json_decode($body, true);
                                $res['DEST_SIAFI'] = \Inspire\DataMapper\DataType\Type::create([
                                    'value' => $data['siafi'] ?? '',
                                    'type' => 'string',
                                    'element' => "DEST_SIAFI"
                                ]);
                            }
                        } catch (RequestException $e) {
                        } catch (\Exception $e) {
                        }
                    }
                    return $result;
                }
            );
            return JsonRenderer::json(
                'Extracted',
                200,
                true,
                json_decode($convertedData, true)
            );
        } catch (\Exception $e) {
            return JsonRenderer::json(
                $e->getMessage(),
                400,
                false
            );
        }
    }
}
