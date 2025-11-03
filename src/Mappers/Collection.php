<?php

declare(strict_types=1);

namespace Inspire\DataMapper\Mappers;

use Inspire\DataMapper\DataType\Type;
use Inspire\DataMapper\Exceptions\{
    LimitViolationException,
    ValueException
};
use Inspire\DataMapper\Mappers\Setup\{
    Collection as SetupCollection,
    Main
};

/**
 * Classe para extração ou compilação de dados em formato de coleções
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
final class Collection extends Base implements MapperInterface
{
    /**
     * Objeto de confguração
     *
     * @var SetupCollection
     */
    protected SetupCollection $setup;

    /**
     * Identificação de propriedades de cada item da collection (chaves de objetos)
     *
     * @var array
     */
    private array $indexes = [];

    /**
     * Construtor de classe
     *
     * @param array $model Modelo de dados esperado como datasource
     * @param SetupCollection $setup Objeto de configuração
     */
    public function __construct(array $model, SetupCollection $setup)
    {
        $this->model = $model;
        $this->setup = $setup;
        /**
         * Em caso de o modelo definir um padrão associativo, extrair cabealho de arquivo e utilizar como indice
         */
        if ($this->setup->getType() == 'assoc') {
            $this->indexes = array_flip(array_column($this->model, 'element'));
        } //Caso contrario, atirbuir ao identificador de cada item o proprio indice numérico 
        else {
            $this->indexes = array_combine(
                array_keys($this->model),
                array_keys($this->model)
            );
        }
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
        $validateInput = ($check === null && $this->setup->isCheckInput())
            || $check === true;
        //Validar dados de entrada
        if ($validateInput) {
            /**
             * Validar o limite de propriedades em um elementos
             */
            $lineCount = count($this->indexes);
            if ($lineCount > $this->setup->getMaxItems()) {
                throw new LimitViolationException("Maximum items violation. Datasource has {$lineCount} elements per line, but only {$this->setup->getMaxItems()} were allowed.");
            }

            /**
             * Validar o limite de elementos
             */
            $registerCount = count($source);
            if ($registerCount > $this->setup->getMaxRecords()) {
                throw new LimitViolationException("Maximum records violation. Datasource has {$registerCount} records, but only {$this->setup->getMaxRecords()} were allowed.");
            }
        }

        $checkFieldLength = $this->setup->getLength();
        if ($this->setup->getType() == 'assoc') {
            /**
             * Pegar chaves associativas com base no nome de elementos definidos no modelo
             */
            $this->indexes = array_flip(array_column($this->model, 'element'));
            /**
             * Iterando elementos da fonte de dados
             */
            foreach ($source as $ln => $registry) {
                $mappedLine = [];
                $mappedLineToExtra = [];
                //Encontrar no modelo a definição correspondente para cada item
                foreach ($this->model as $model) {
                    $elementDefinition = $model;
                    /**
                     * Se o elemento não estiver definido no modelo, utilizar o valor default definido na configuração de modelo
                     */
                    $elementDefinition['value'] = $registry[$model['element']] ?? $this->setup->getDefault();

                    /**
                     * Validar o tamanho limite de cada campo
                     */
                    if (
                        $validateInput &&
                        $checkFieldLength != null &&
                        $checkFieldLength < mb_strlen($elementDefinition['value'])
                    ) {
                        $ln++;
                        throw new LimitViolationException("Maximum length violation at index {$ln}, field {$elementDefinition['element']}. Datasource has " . mb_strlen($elementDefinition['value']) . " bytes, but only {$checkFieldLength} were allowed.");
                    }
                    /**
                     * Cria um pbjeto Type usando esta definição
                     */
                    $element = Type::create($elementDefinition);
                    //Validar valor de campo, se validação habilitada
                    if ($validateInput) {
                        // try {
                        $element->validate();
                        // } catch (\Exception $e) {
                        //     // echo "{$e->getMessage()} - {$e->getLine()}\n";
                        // }
                    }
                    //Adicionar propriedade ao objeto
                    // $mappedLine[$this->indexes[$model['element']]] = $element;
                    $mappedLine[$model['element']] = $element;
                }
                /**
                 * Incluir campos extra do modelo, para esta item criado
                 */
                if (!empty($this->registeredExtras)) {
                    $mappedLine = $this->evalueteExtras($mappedLine);
                }
                /**
                 * Incluir objeto à lista de dados extraidos
                 */
                $this->sourceMap[] = $mappedLine;
            }
        } else {
            foreach ($source as $registry) {
                /**
                 * Para indices numericos, considerar que cada propriedade estará na mesmo ordem de definição no modelo
                 */
                $registry = array_values($registry);
                $mappedLine = [];
                $field = 0;
                //Percorrer campos definidos no modelo e processar valor correspondente na fonte de dados
                foreach ($this->model as $ln => $model) {
                    $elementDefinition = $this->model[$field];
                    /**
                     * Caso não haja um valor definido na fonte de dados, usar valor padrão definido na configuração
                     */
                    $elementDefinition['value'] = $registry[$field] ?? $this->setup->getDefault();

                    /**
                     * Validar o tamanho limite de cada campo
                     */
                    if (
                        $validateInput &&
                        $checkFieldLength != null &&
                        $checkFieldLength < mb_strlen($elementDefinition['value'])
                    ) {
                        $ln++;
                        throw new LimitViolationException("Maximum length violation at index {$ln}, field {$elementDefinition['element']}. Datasource has " . mb_strlen($elementDefinition['value']) . " bytes, but only {$checkFieldLength} were allowed.");
                    }
                    /**
                     * Cria um objeto Type usando esta definição
                     */
                    $element = Type::create($elementDefinition);
                    //Validar valor de campo, se validação habilitada
                    if ($validateInput) {
                        // try {
                        $element->validate();
                        // } catch (\Exception $e) {
                        //     // echo "{$e->getMessage()} - {$e->getLine()}\n";
                        // }
                    }
                    //Adicionar propriedade ao objeto
                    $mappedLine[$field] = $element;
                    $mappedLineToExtra[$model['element']] = $element;
                    $field++;
                }
                /**
                 * Ordenar dados por chave e incluir objeto na lista de dados extraidos
                 */
                ksort($mappedLine);

                /**
                 * Incluir campos extra do modelo, para esta item criado
                 * Como não existe referencia de posição por nome de elemento, os campos extras serão sempre inseridos no final
                 */
                if (!empty($this->registeredExtras)) {
                    $mappedLine = array_merge($mappedLine, array_values($this->evalueteExtras($mappedLineToExtra)));
                }
                $this->sourceMap[] = $mappedLine;
            }
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
        $dataOut = [];
        /**
         * Lista indices definidos no modelo
         */
        $fieldIndex = array_column($this->model, 'element');
        //Iterando entre objetos coletados da fonte
        $inputData = $source;
        if ($source instanceof Base) {
            $inputData = $source->sourceMap;
        }
        foreach ($inputData as $register) {
            $lineData = [];
            //Percorrer lista de camposs do modelo
            foreach ($register as $field) {
                /**
                 * Para cada ocorrencia do elemento na lista de campos do modelo...
                 */
                foreach (array_keys($fieldIndex, $field->getElement()) as $key) {
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
                $lineData[$idx] = $fmodel['default'] ?? $this->setup->getDefault() ?? '';
            }
            /**
             * Ordenar dados por chave e incluir objeto na lista de dados extraidos
             */
            ksort($lineData);
            //Se fipo for associativo, reindexa utilizando as chaves associativas
            if ($this->setup->getType() == 'assoc') {
                $lineData = array_combine($fieldIndex, $lineData);
            }
            //Adicionar propriedade ao objeto
            $dataOut[] = $lineData;
        }
        /**
         * Compila dados de saida em uma string JSON 
         */
        $this->built = json_encode(Base::convertToUtf8Recursive($dataOut));
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
