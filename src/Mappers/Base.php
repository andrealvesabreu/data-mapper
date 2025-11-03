<?php

declare(strict_types=1);

namespace Inspire\DataMapper\Mappers;

use Closure;
use Inspire\DataMapper\DataType\Type;
use Inspire\DataMapper\Mappers\Setup\Main;
use Jackiedo\XmlArray\Xml2Array;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Classe base para as funções de extração e compilação de dados
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
abstract class Base
{
    /**
     * Modelo de documento de origem na extração ou de destino na compilação
     *
     * @var array
     */
    protected array $model = [];

    /**
     * Instancia de classe para objeto de extração de dados
     *
     * @var Base|null|null
     */
    private static ?Base $source = null;

    /**
     * Instancia de classe para objeto de compilação de dados
     *
     * @var Base|null|null
     */
    private static ?Base $target = null;

    /**
     * Lista de objetos Enum definidos no modelo que está sendo utilizado
     *
     * @var array
     */
    protected static $registeredEnums = [];

    /**
     * Lista de objetos de registros definidos no modelo que está sendo utilizado
     * Um registro é uma seção de modelo que pode se repetir mais de uma vez, sendo possivel referenciá-lo no modelo ao inves de copiar em cada lugar em ocorre
     *
     * @var array
     */
    protected static $registeredRegisters = [];

    /**
     * Campos extras no modelo
     *
     * @var array
     */
    protected array $registeredExtras = [];

    /**
     * Lista de objetos construidos a partir da extração de dados do datasource, em que cada propriedade é um objeto do tipo Type
     *
     * @var array
     */
    protected array $sourceMap = [];

    /**
     * Lista de objetos compilados ao final de um processo de conversão completa (extraão de modelo de origem para compilação em modelo de destino) 
     *
     * @var string
     */
    protected ?string $built = null;

    /**
     * Extrair dados do datasource e retornar lista de objetos com todas as propriedades do tipo Type
     *
     * @param array $sourceModel Modelo (esquema) com especificaão de estrutura para extração de dados
     * @param array $data Dados para extração
     *
     * @return array
     */
    public static function extract(array $sourceModel, array|string $data, ?bool $check = null, ?Closure $fn = null): array
    {
        $source = self::createParser($sourceModel);
        $source->map(self::getData($data, $source), $check);
        if ($fn !== null) {
            $source->sourceMap = $fn($source->sourceMap);
        }
        return $source->sourceMap;
    }

    /**
     * Extrair dados do datasource e retornar coleções indexadas com o nome dos elementos
     *
     * @param array $sourceModel Modelo (esquema) com especificaão de estrutura para extração de dados
     * @param array $data Dados para extração
     *
     * @return array
     */
    public static function extractToArray(array $sourceModel, array|string $data, ?bool $check = null, ?Closure $fn = null): array
    {
        $intermediate = self::extract($sourceModel, $data, $check, $fn);
        array_walk($intermediate, function (&$collection) {
            $temp = [];
            foreach ($collection as $key => $item) {
                $temp[$key] = $item->getValue()->__toString();
            }
            $collection = $temp;
        });
        return $intermediate;
    }

    /**
     * Extrair dados de um datasource, usando o modelo de entrada para leitura e converte para um modelo de destino
     *
     * @param array $sourceModel Modelo (esquema) com especificaão de estrutura para extração de dados
     * @param array $targetModel Modelo (esquema) com especificaão de estrutura para compilação de dados
     * @param array|string $data Dados para extração
     *
     * @return string|null
     */
    public static function convert(array $sourceModel, array  $targetModel, array|string $data, ?Closure $fn = null): ?string
    {
        /**
         * Extração de dados
         */
        self::$source = self::createParser($sourceModel);
        self::$source->map(self::getData($data));
        if ($fn !== null) {
            self::$source->sourceMap = $fn(self::$source->sourceMap);
        }
        /**
         * Compilação de dados
         */
        self::$target = self::createParser($targetModel);
        self::$target->build(self::$source);
        return self::$target->getBuilt();
    }

    /**
     * Captura de dados do datasource, de acordo com a classe de extração que será utilizada
     *
     * @param array|string $data Dados para extração
     * @param mixed|null $source Nome da classe queserá usada para extração
     *
     * @return array
     */
    private static function getData(array|string $data, mixed $source = null): array
    {
        $dataSource = $data;
        switch ((new \ReflectionClass($source ?? self::$source))->getShortName()) {
            case 'Csv':
            case 'Proceda':
                //Lendo de um arquivo
                if (is_string($data)) {
                    if (is_file($data)) {
                        $dataSource = file($data);
                    } else {
                        $dataSource = explode(PHP_EOL, $data);
                    }
                }
                break;
            case 'Collection':
                //Lendo de um arquivo
                if (is_string($data) && is_file($data)) {
                    $dataSource = include $data;
                }
                break;
            case 'Xml':
                //Lendo de um arquivo
                if (is_string($data) && is_file($data)) {
                    $data = file_get_contents($data);
                }
                $dataSource = Xml2Array::convert($data)->toArray();
                break;
            case 'Json':
                //Lendo de um arquivo
                if (is_string($data)) {
                    if (is_file($data)) {
                        $data = file_get_contents($data);
                    }
                    //Can also set string directly
                    $dataSource = json_decode($data, true);
                }
                break;
        }
        return $dataSource;
    }

    /**
     * DEvolver dados no formato de modelo de saida no caso de conversão completa entre dois modelos
     *
     * @return string|null
     */
    public function getBuilt(): ?string
    {
        return $this->built;
    }

    /**
     * Devolve dados em formato intermediário, após extração do datasource (modelo de origem) em coleções de objetos Type
     *
     * @return array
     */
    public static function getMappedData(): array
    {
        return self::$source->sourceMap;
    }

    /**
     * Cria instancias de objetos das classes necessárias para extraão/conversão
     *
     * @param array $model Recebe um array com estrutura do modelo, devendo ter obrigatoriamente os campos parser, setup e definitions
     *
     * @return Base
     */
    protected static function createParser(array $model): Base
    {
        /**
         * Criar parser de acordo com definição de modelo
         */
        $class = "\\Inspire\\DataMapper\\Mappers\\" . ucfirst($model['parser']);
        $classSetup = "\\Inspire\\DataMapper\\Mappers\\Setup\\" . ucfirst($model['parser']);
        $parser = new $class(
            $model['definitions'],
            new $classSetup($model['setup'])
        );
        //Carregar definições de enum, caso existam
        if (isset($model['enums'])) {
            Base::$registeredEnums = $model['enums'];
        }
        //Carregar definições de registers, caso existam
        if (isset($model['registers'])) {
            Base::$registeredRegisters = $model['registers'];
        }
        //Carregar definições de registers, caso existam
        if (isset($model['extra'])) {
            $parser->registeredExtras = $model['extra'];
        }
        return $parser;
    }

    /**
     * Definição de função para mapeamento e extraão de dados do datasource
     *
     * @param array $source Dados de entrada
     * @param bool $check Indicador apra validar ou não dados durante a extração
     *
     * @return void
     */
    abstract public function map(array $source, bool $check = true): void;

    /**
     * Define função para compilação de dados para formato de destino
     *
     * @param Base $source Objeto de tipo Base cque já tenha dados de extraão prévia
     *
     * @return void
     */
    abstract public function build(Base $source): void;

    /**
     * Definição de função para retornar configuração
     *
     * @return Main
     */
    abstract public function getSetup(): Main;

    /**
     * Método para retornar objetos Enum definidos
     *
     * @param string|null $key Indice para consulta de Enum's registrados. Se omitido, devolver todos os items
     *
     * @return array|null
     */
    public static function getRegisteredEnums(?string $key = null): ?array
    {
        if ($key !== null) {
            return self::$registeredEnums[$key] ?? null;
        }
        return self::$registeredEnums;
    }

    /**
     * Método para sanitização de dados, forçando codificação para UTF-8
     *
     * @param mixed $data Dados para conversão em UTF8, podendo ser array, object ou string
     *
     * @return mixed
     */
    protected static function convertToUtf8Recursive($data): mixed
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::convertToUtf8Recursive($value);
            }
        } elseif (is_object($data)) {
            $vars = get_object_vars($data);
            foreach ($vars as $key => $value) {
                $data->$key = self::convertToUtf8Recursive($value);
            }
        } elseif (is_string($data)) {
            $current_encoding = mb_detect_encoding($data, 'UTF-8, ISO-8859-1, Windows-1252', true);
            if ($current_encoding === false) { // Could not detect or it's a mix
                $data = iconv('UTF-8', 'UTF-8//IGNORE', $data);
            } elseif ($current_encoding !== 'UTF-8') {
                $data = mb_convert_encoding($data, 'UTF-8', $current_encoding);
            }
        }
        return $data;
    }

    /**
     * @param array $registry
     *
     * @return array
     */
    protected function evalueteExtras(array $registry): array
    {
        $mapValues = array_map(function ($item) {
            return $item->getValue()->getValue();
        }, $registry);
        $expressionLanguage  = $this->getExpressionLanguage();
        foreach ($this->registeredExtras as $element => $item) {
            if (isset($item['expression']) && !empty($item['expression'])) {
                $item['value'] = $expressionLanguage->evaluate($item['expression'], $mapValues);
            } elseif (isset($item['default']) && !empty($item['default'])) {
                $item['value'] = $item['default'];
            }
            $registry[$element] = Type::create($item, false);
            $mapValues[$element] = $item['value'];
        }
        return $registry;
    }

    /**
     * Método para listar elementos obrigatórios dentro de um registro
     *
     * @param array $data Coleção de dados para avaliação, onde se deve verificar os campos que são required
     * @param bool $isArray Indicador se os dados de entrada estão definidos como array ou como Type
     *
     * @return array
     */
    protected function getRequiredElements(array $data, bool $isArray = true): array
    {
        if ($isArray) {
            $filtered = array_filter($data, function ($item) {
                return isset($item['required']) && $item['required'] === true;
            });
            return array_column($filtered, 'element');
        } else {
            $filtered = array_filter($data, function ($item) {
                return $item->isRequired();
            });
            return array_map(function ($item) {
                return $item->getElement();
            }, $filtered);
        }
    }

    /**
     * @param array $modelSetup
     * @param array $setup
     *
     * @return array
     */
    protected function checkSetup(array $modelSetup, array $setup): array
    {
        $allowedKeys = array_keys($modelSetup);
        $inputKeys = array_keys($setup);
        $extraKeys = array_diff($inputKeys, $allowedKeys);
        if (!empty($extraKeys)) {
            return [
                'status' => false,
                'message' => 'Setup input contains invalid properties: ' . implode(', ', $extraKeys)
            ];
        }
        // Iterate through the schema to check required fields and validate existing values
        foreach ($modelSetup as $field => $rules) {
            $required = $rules['required'] ?? false;
            $type = $rules['type'] ?? 'string';
            $isPresent = array_key_exists($field, $setup);
            /**
             * Campos obrigatorios não informados
             */
            if ($required && !$isPresent) {
                return [
                    'status' => false,
                    'message' => "Required property '{$field}' is missing in setup."
                ];
            }

            if ($isPresent) {
                $value = $setup[$field];
                switch ($type) {
                    case 'int':
                        /**
                         * Se for um inteiro, validar tipo de dados
                         */
                        if (!filter_var($value, FILTER_VALIDATE_INT) && $value !== 0) {
                            return [
                                'status' => false,
                                'message' => "Property '{$field}' must be an integer in setup."
                            ];
                        }
                        /**
                         * Validar os limites, se forem definidos
                         */
                        $intValue = (int)$value;
                        if (isset($rules['minimum']) && $intValue < $rules['minimum']) {
                            return [
                                'status' => false,
                                'message' => "Property '{$field}' ({$value}) is below the minimum allowed value of {$rules['minimum']} in setup."
                            ];
                        }
                        if (isset($rules['maximum']) && $intValue > $rules['maximum']) {
                            return [
                                'status' => false,
                                'message' => "Property '{$field}' ({$value}) is above the maximum allowed value of {$rules['maximum']} in setup."
                            ];
                        }
                        break;
                    case 'string':
                        /**
                         * Para o tipo string, validar apenas se o conteudo é uma string
                         */
                        if (!is_string($value) && $value !== null) {
                            return [
                                'status' => false,
                                'message' => "Property '{$field}' must be a string in setup."
                            ];
                        }
                        break;
                    case 'bool':
                        /**
                         * Booelan, validar apenas se o tipo de dado esta correto
                         */
                        if (!is_bool($value)) {
                            return [
                                'status' => false,
                                'message' => "Property '{$field}' must be a native boolean (true or false) in setup."
                            ];
                        }
                        break;

                    case 'choice':
                        /**
                         * Opções pre definidas
                         */
                        $options = $rules['options'] ?? [];
                        if (!in_array($value, $options, true)) {
                            return [
                                'success' => false,
                                'error' => "Property '{$field}' ('{$value}') is not a valid choice in setup. Must be one of: " . implode(', ', $options)
                            ];
                        }
                        break;
                    default:
                        break;
                }
            }
        }
        return ['status' => true];
    }

    /**
     * @param array $model
     * @param array $setup
     *
     * @return array
     */
    protected function checkField(array $model, array $definition): array
    {
        $allowedKeys = array_keys($model);
        $inputKeys = array_keys($definition);
        $extraKeys = array_diff($inputKeys, $allowedKeys);
        $fieldName = $definition['element'] ?? 'unknown';
        if (!empty($extraKeys)) {
            return [
                'status' => false,
                'message' => "Input field contains invalid properties in '{$fieldName}': " . implode(', ', $extraKeys)
            ];
        }
        /**
         * Iterar pelo esquema para verificar os campos obrigatórios e validar os valores existentes
         */
        foreach ($model as $field => $rules) {
            $required = $rules['required'] ?? false;
            $type = $rules['type'] ?? 'string';
            $isPresent = array_key_exists($field, $definition);
            /**
             * Campos obrigatorios não informados
             */
            if ($required && !$isPresent) {
                return [
                    'status' => false,
                    'message' => "Required property '{$field}' is missing in field '{$fieldName}'."
                ];
            }

            if ($isPresent) {
                $value = $definition[$field];
                switch ($type) {
                    case 'int':
                        /**
                         * Se for um inteiro, validar tipo de dados
                         */
                        if (!filter_var($value, FILTER_VALIDATE_INT) && $value !== 0) {
                            return [
                                'status' => false,
                                'message' => "Property '{$field}' must be an integer in element '{$fieldName}'."
                            ];
                        }
                        break;
                    case 'float':
                        /**
                         * Se for um inteiro, validar tipo de dados
                         */
                        if (!filter_var($value, FILTER_VALIDATE_FLOAT) && $value !== 0) {
                            return [
                                'status' => false,
                                'message' => "Property '{$field}' must be an decimal in element '{$fieldName}'."
                            ];
                        }
                        break;
                    case 'string':
                        /**
                         * Para o tipo string, validar apenas se o conteudo é uma string
                         */
                        if (!is_string($value) && $value !== null) {
                            return [
                                'status' => false,
                                'message' => "Property '{$field}' must be a string in element '{$fieldName}'."
                            ];
                        }
                        break;
                    case 'datetime':
                        /**
                         * Para o tipo string, validar apenas se o conteudo é uma string
                         */
                        if (!is_string($value) && $value !== null) {
                            return [
                                'status' => false,
                                'message' => "Property '{$field}' must be a datetime in element '{$fieldName}'."
                            ];
                        }
                        break;
                    case 'numeric':
                        /**
                         * Para o tipo string, validar apenas se o conteudo é uma string
                         */
                        if (!is_string($value) && $value !== null) {
                            return [
                                'status' => false,
                                'message' => "Property '{$field}' must be a numeric in element '{$fieldName}'."
                            ];
                        }
                        break;
                    case 'bool':
                        /**
                         * Booelan, validar apenas se o tipo de dado esta correto
                         */
                        if (!is_bool($value)) {
                            return [
                                'status' => false,
                                'message' => "Property '{$field}' must be a native boolean in element '{$fieldName}'."
                            ];
                        }
                        break;
                }
            }
        }
        return ['status' => true];
    }

    /**
     * @return ExpressionLanguage
     */
    private function getExpressionLanguage(): ExpressionLanguage
    {
        $expressionLanguage = new ExpressionLanguage();
        /**
         * Regitrar funções nativas
         */
        $expressionLanguage->register(
            'substr',
            function (...$arguments) {},
            function (array $variables, ...$arguments) {
                return substr(...$arguments);
            }
        );
        $expressionLanguage->register(
            'strlen',
            function (...$arguments) {},
            function (array $variables, ...$arguments) {
                return strlen(...$arguments);
            }
        );
        $expressionLanguage->register(
            'empty',
            function (...$arguments) {},
            function (array $variables, ...$arguments) {
                return empty($arguments[0]);
            }
        );
        $expressionLanguage->register(
            'trim',
            function (...$arguments) {},
            function (array $variables, ...$arguments) {
                $args = [
                    $arguments[0] ?? '', //Input string
                    $arguments[1] ?? " \n\r\t\v\x00" //Caracere a remover
                ];
                switch (strtoupper($arguments[2] ?? 'B')) {
                    case 'L':
                        return ltrim(...$args);
                    case 'R':
                        return rtrim(...$args);
                    case 'B':
                    default:
                        return trim(...$args);
                }
            }
        );
        $expressionLanguage->register(
            'concat',
            function (...$arguments) {},
            function (array $variables, ...$arguments) {
                return implode('', $arguments);
            }
        );
        return $expressionLanguage;
    }
}
