<?php

ini_set('display_errors', true);
error_reporting(E_ALL);

require dirname(__DIR__) . '/vendor/autoload.php';
define('DIR_MODELS', dirname(__DIR__) . '/models');

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;

$containerBuilder = new ContainerBuilder();
$container = $containerBuilder->build();
AppFactory::setContainer($container);
$app = AppFactory::create();
$app->addRoutingMiddleware();

/**
 * Consulta elemento especifico ou todos os elementos do namespace
 */
$app->get('/element/{namespace}[/{element}]', Api\DataMapper\Element\Action::class . ':get');
/**
 * Insere um elemento
 * A função validará a estrutura de definição e os campos necessários para cada tipo de parser indicado
 */
$app->post('/element', Api\DataMapper\Element\Action::class . ':post');
/**
 * Exclui um elemento
 */
$app->delete('/element/{namespace}/{element}', Api\DataMapper\Element\Action::class . ':delete');
/**
 * Consulta modelo especifico, indicando o namespace.
 * Assim como os elementos, podem existir diferentes modelos com o mesmo nome,
 * desde que estejam em namespaces separados.
 * Para melhor visibilidade, somente será permitido consultar um modelo por vez
 */
$app->get('/model/{name}[/{parser}]', Api\DataMapper\Model\Action::class . ':get');
/**
 * Insere um modelo
 * A função validará a estrutura e a existencia de todos os elementos necessarios para a compilação de modelo final
 */
$app->post('/model', Api\DataMapper\Model\Action::class . ':post');
/**
 * Exclui um modelo, indicando obrigatóriamente o nome e namespace dele
 */
$app->delete('/model/{name}[/{parser}]', Api\DataMapper\Model\Action::class . ':delete');
/**
 * Compilar um modelo e salvar arquivo
 */
$app->post('/build/{name}/{parser}', Api\DataMapper\Model\Action::class . ':build');
/**
 * Extrair dados informações dos dados de entrada
 */
$app->post('/extract', Api\DataMapper\Model\Action::class . ':extract');
/**
 * Conversão de modelos
 */
$app->post('/convert', Api\DataMapper\Model\Action::class . ':convert');

$app->run();
