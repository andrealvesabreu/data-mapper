<?php

declare(strict_types=1);

namespace Inspire\DataMapper\Mappers;

use Inspire\DataMapper\DataType\Type;
use Inspire\DataMapper\Exceptions\{
    LimitViolationException,
    ValueException
};
use Inspire\DataMapper\Mappers\Setup\{
    Csv as SetupCsv,
    Main
};

/**
 * Classe para extração ou compilação de dados em formato CSVX
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
final class Csv extends Base implements MapperInterface
{

    /**
     * Objeto de confguração
     *
     * @var SetupCsv
     */
    protected SetupCsv $setup;

    /**
     * @var array
     */
    private array $indexes = [];

    /**
     * Construtor de classe
     *
     * @param array $model Modelo de dados esperado como datasource
     * @param SetupCsv $setup Objeto de configuração
     */
    public function __construct(array $model, SetupCsv $setup)
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
         * Definir chaves para objetos que serão extraidos
         */
        $this->setIndexes($source);
        $collection = [];

        $validateInput = ($check === null && $this->setup->isCheckInput())
            || $check === true;
        //Validar dados de entrada
        if ($validateInput) {
            $lineCount = count($this->indexes);
            if ($lineCount > $this->setup->getMaxItems()) {
                throw new LimitViolationException("Maximum items violation. Datasource has {$lineCount} elements per line, but only {$this->setup->getMaxItems()} were allowed.");
            }

            $registerCount = count($source);
            if ($registerCount > $this->setup->getMaxRecords()) {
                throw new LimitViolationException("Maximum records violation. Datasource has {$registerCount} records, but only {$this->setup->getMaxRecords()} were allowed.");
            }
        }

        $checkFieldLength = $this->setup->getLength();
        foreach ($source as $ln => $data) {
            //Ignorar linhas em branco na fonte de dados
            if (empty($data)) {
                continue;
            }
            /**
             * Extrair dados da linha utilizando configuração (separador, caracter de encapsulamento e escape)
             */
            $lineData = array_combine(
                $this->indexes,
                str_getcsv(
                    $data,
                    $this->setup->getSeparator(),
                    $this->setup->getEnclosure(),
                    $this->setup->getEscape()
                )
            );
            /**
             * Validar tamanho limite de cada campo na linha, se aplicável
             */
            if ($checkFieldLength != null) {
                $ln++;
                foreach ($lineData as $fi => $fieldData) {
                    if ($checkFieldLength < mb_strlen($fieldData)) {
                        if (is_integer($fi)) {
                            $fi++;
                        }
                        throw new LimitViolationException("Maximum length violation at line {$ln}, field {$fi}. Datasource has " . mb_strlen($data) . " bytes, but only {$checkFieldLength} were allowed.");
                    }
                }
            }
            /**
             * Incluir linha em um array para usar a classe de Collection na trataiva de dados, 
             * pois estruturalmente os dois modelos são muito similares.
             */
            $collection[] = $lineData;
        }
        /**
         * Definir uma especificação de configuração para a classe COllection com base na configuração atual
         */
        $sourceModel = [
            'parser' => 'collection',
            'setup' => [
                'fillMissing' => $this->setup->isFillMissing(), //Auto fill elements missing that have a default value in mnodel
                'default' => $this->setup->getDefault(), //Use for option fillMissing= true and no default value set in field
                'checkInput' => $this->setup->isCheckInput(), //Check data while read from source
                'checkOutput' => $this->setup->isCheckOutput(), //Check data while writing to destination
                'length' => $this->setup->getLength(), //maxlenght of each size
                'maxItems' => $this->setup->getMaxItems(), //maximum number of elements in a line
                'maxRecords' => $this->setup->getMaxRecords(), //Maximum number of records
                'type' => $this->setup->getType(), //Mapper is based on key name or numeric index
            ],
            'definitions' => $this->model
        ];
        /**
         * Utilizar classe Collection para fazer a montagem de dados intermediária
         */
        $collectionMapper = Base::createParser($sourceModel);
        $collectionMapper->registeredExtras = $this->registeredExtras;
        $collectionMapper->map($collection, $check);
        $this->sourceMap = $collectionMapper->sourceMap;
    }

    /**
     * Métoos para definir as chaves de objeto com base na configuração (numérico ou associativo)
     *
     * @param array $source Dados de entrada
     *
     * @return void
     */
    private function setIndexes(array &$source): void
    {
        /**
         * Se arquivo tiver um cabeçalho, remover esta linha e extrair as chaves
         */
        if ($this->setup->getHeader()) {
            $this->indexes = str_getcsv(
                array_shift($source),
                $this->setup->getSeparator(),
                $this->setup->getEnclosure(),
                $this->setup->getEscape()
            );
        } //Se não há cabeçalho, usar chaves numéricas com base na primeira linha 
        else {
            $this->indexes = array_keys(
                str_getcsv(
                    $source[0],
                    $this->setup->getSeparator(),
                    $this->setup->getEnclosure(),
                    $this->setup->getEscape()
                )
            );
        }
    }

    /**
     * Compila dados para formato de destino
     *
     * @param Base $source Objeto do tipo Base com dados extraidos previamente de modelo de origem 
     *
     * @return void
     */
    public function build(Base $source): void
    {
        /**
         * Abrir um stream em memória para escrever dados como um arquivo
         */
        $fpCSV = fopen('php://temp', 'r+');
        $fieldIndex = array_column($this->model, 'element');
        /**
         * Se o modelo indica a presena de cabeçalho, inserir um linha inicial com as chaves de objeto como nome de colunas
         */
        if ($this->setup->getHeader()) {
            fputcsv(
                $fpCSV,
                $fieldIndex,
                $this->setup->getSeparator(),
                $this->setup->getEnclosure(),
                $this->setup->getEnclosure()
            );
        }
        /**
         * Percorrer dados recebidos para compilar cada linha do CSV de saida
         */
        $inputData = $source;
        if ($source instanceof Base) {
            $inputData = $source->sourceMap;
        }
        foreach ($inputData as $register) {
            $lineData = [];
            /**
             * Percorrendo cada propriedade do objeto atual
             */
            foreach ($register as $field) {
                /**
                 * Encontrar elemento na lista de campos do modelo
                 */
                $whereIsTheFields = array_keys($fieldIndex, $field->getElement());
                //Para cada ocorrencia dele, preencher com os dados recebidos
                foreach ($whereIsTheFields as $key) {
                    /**
                     * Tentar ler a definiaõ do campo por ordem de prioridade, cond=siderando primeiro a chave de objeto e depois o nome de elemento
                     */
                    $elementDefinition = $this->model[$key] ?? $this->model[$field->getElement()] ?? false;
                    //Se o elemento não existir no modelo, ignorar
                    if ($elementDefinition) {
                        /**
                         * Atribuir valor do objeto de entrada ao modelo de objeto de saida
                         */
                        $elementDefinition['value'] = $field->getValue()->getValue();
                        $typeData = Type::create($elementDefinition, false);
                        //Validar valor de campo, se validação habilitada
                        if ($this->setup->isCheckOutput()) {
                            $typeData->validate();
                        }
                        //Adicionar propriedade ao objeto
                        $lineData[$key] = $typeData->getValue()->__toString();
                    }
                }
            }
            /**
             * Preencher cmapos definidos no modelo, mas ausente no objeto
             */
            foreach ($this->model as $idx => $fmodel) {
                //Ignorar itens que já foram incluidos
                if (isset($lineData[$idx])) {
                    continue;
                }
                /**
                 * Se for obrigatório e não houver um valor padrão, é um erro
                 */
                if (
                    isset($fmodel['required']) &&
                    $fmodel['required'] &&
                    !isset($fmodel['default']) &&
                    $this->setup->getDefault() === null
                ) {
                    throw new ValueException("The field {$fmodel['element']} is required, but it have not a default value.");
                }
                /**
                 * Incluir campo vazio, mesmo que não obrigatorio, pois é necessário manter as posições correspondentes
                 */
                $lineData[$idx] = $fmodel['default'] ?? '';
            }
            /**
             * Ordenar dados por chave
             */
            ksort($lineData);
            //Escreve dados em arquivo em memória
            fputcsv(
                $fpCSV,
                $lineData,
                $this->setup->getSeparator(),
                $this->setup->getEnclosure(),
                $this->setup->getEnclosure()
            );
        }
        //Voltar ponteiro para o início do arquivo
        rewind($fpCSV);
        //Ler arquivo em memória para string de saida
        $this->built = stream_get_contents($fpCSV);
        // Fechar ponteiro de arquivo para liberalão de memória
        fclose($fpCSV);
    }

    /**
     * Devolver configuração aplicada
     *
     * @return Main
     */
    public function getSetup(): Main
    {
        return $this->setup;
    }

    /**
     * Valida estrutura de modelo
     *
     * @param array $model Modelo de dados para avaliação
     *
     * @return array
     */
    public function checkSchema(array $model): array
    {
        $setup = $this->checkSetup($this->setup::PROPERTIES['setup'], $model['setup']);
        if ($setup['status'] == 0) {
            return $setup;
        }
        foreach ($model['definitions'] as $definition) {
            $type = $definition['type'];
            unset($definition['type']);
            if (isset($this->setup::PROPERTIES['type'][$type])) {
                $setup = $this->checkField(
                    $this->setup::PROPERTIES['type'][$type],
                    $definition
                );
                if ($setup['status'] == 0) {
                    return $setup;
                }
            } else {
                return [
                    'status' => false,
                    'message' => "Invalid data type: {$type}"
                ];
            }
        }
        return [
            'status' => true,
            'message' => 'OK'
        ];
    }
}
