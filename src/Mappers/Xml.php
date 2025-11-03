<?php

declare(strict_types=1);

namespace Inspire\DataMapper\Mappers;

use Inspire\DataMapper\Mappers\Setup\Xml as SetupXml;

/**
 * Classe para extração ou compilação de dados em formato XML
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
class Xml extends Json implements MapperInterface
{

    /**
     * Construtor de classe
     *
     * @param array $model Modelo de dados esperado como datasource
     * @param SetupXml $setup Objeto de configuração
     */
    public function __construct(array $model, SetupXml $setup)
    {
        $this->model = $model;
        $this->setup = $setup;
        $this->setup->check();
    }

    /**
     * Método para extrair dados do datasource
     *
     * @param array $source Dados de entrada
     * @param bool|null $check indicador para validar ou não dados durante a leitura
     *
     * @return void
     */
    public function map(array $source, ?bool $check = null): void
    {
        /**
         * Usar parser JSON para extrair dados de XML
         */
        $sourceModel = [
            'parser' => 'json',
            'setup' => [
                'fillMissing' => $this->setup->isFillMissing(),
                'checkInput' => $this->setup->isCheckInput(),
                'checkOutput' => $this->setup->isCheckOutput(),
                'length' => $this->setup->getLength(),
                'pivot' => $this->setup->getPivot(),
                'default' => $this->setup->getDefault(),
                'maxDepth' => $this->setup->getMaxDepth(),
                'maxItems' => $this->setup->getMaxItems(),
                'maxRecords' => $this->setup->getMaxRecords()
            ],
            'definitions' => $this->model
        ];
        $jsonMapper = Base::createParser($sourceModel);
        $jsonMapper->registeredExtras = $this->registeredExtras;
        $jsonMapper->map(
            $this->wrapNestedElements($source),
            $check
        );
        $this->sourceMap = $jsonMapper->sourceMap;
    }

    /**
     * Mover a chave para um nível mais baixo na coleção numérica. Ex.:
     * $data  = [
     *      'parentKey' => [
     *          [
     *              'ChildKey' => 1
     *          ],
     *          [
     *              'ChildKey' => 2
     *          ]
     *      ]
     *  ];
     *  $data  = [
     *      [
     *          'parentKey' => [
     *              'ChildKey' => 1
     *          ],
     *      ],
     *      [
     *          'parentKey' => [
     *              'ChildKey' => 2
     *          ]
     *      ]
     *  ];
     * @param array $inputArray Array de dados no qual os indices devem ser reajustados
     *
     * @return array
     */
    private function wrapNestedElements(array $inputArray): array
    {
        $outputArray = [];
        /**
         * Para cada nivel de array processado, continuar chamando função recursivamente até que não hajam mais arrays em níveis abaixo
         */
        foreach ($inputArray as $key => $value) {
            /**
             * Detecthando um array de chaves numéricas:
             * Se variável for um array;
             * Se array não for vazio;
             * Se as chaves do array forem uma sequancia de números inteiros iniciando em 0 até o total de elementos do array - 1
             */
            if (is_array($value)) {
                if (
                    !empty($value) &&
                    array_keys($value) === range(0, count($value) - 1)
                ) {
                    $wrappedElements = [];
                    foreach ($value as $item) {
                        $processedItem = $this->wrapNestedElements($item);
                        $wrappedElements[] = [$key => $processedItem];
                    }
                    $outputArray = array_merge($outputArray, $wrappedElements);
                } else {
                    $outputArray[$key] = $this->wrapNestedElements($value);
                }
            } else {
                $outputArray[$key] = $value;
            }
        }
        return $outputArray;
    }
}
