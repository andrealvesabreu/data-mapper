<?php

declare(strict_types=1);

namespace Inspire\DataMapper\Mappers;

use Illuminate\Support\Arr;
use Inspire\DataMapper\DataType\Type;
use Inspire\DataMapper\Exceptions\LimitViolationException;
use Inspire\DataMapper\Mappers\Setup\{
    Json as SetupJson,
    Xml as SetupXml,
    Main
};
use Tree\Node\Node;


/**
 * Classe para extração ou compilação de dados em formato Json
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
class Json extends Base implements MapperInterface
{

    /**
     * Objeto de confguração
     *
     * @var SetupJson
     */
    protected SetupJson|SetupXml $setup;

    /**
     * Cria um índice de modelo virtual, definindo caminhos para cada elemento
     *
     * @var array
     */
    private array $virtualModel = [];

    /**
     * Lista de objetos compilados do após leitura do datasource
     * Cada elemento pivô encontrado será processado com todas os itens relacionados e listados neste atributo
     */
    private array $elementList = [];

    /**
     * Construtor de classe
     *
     * @param array $model Modelo de dados esperado como datasource
     * @param SetupJson $setup Objeto de configuração
     */
    public function __construct(array $model, SetupJson $setup)
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
        $validateInput = ($check === null && $this->setup->isCheckInput())
            || $check === true;
        /**
         * Mextrair todos os caminhos de cada elemento no datasource, criando um mindice
         */
        $this->virtualizeModel($this->model);
        /**
         * Criar uma arvore binária para virtualizar a estrutura do JSON relacionando os atributos relacionados a cada elemento pivo
         */
        $this->buildTreeFromSource($source, $validateInput);
        /**
         * Para cada elemento pivo encontrado, identificar todos os elementos relacionados a ele (nós filhos e anxwarais) para criar um objeto simples com toda as propriedades
         */
        foreach ($this->elementList as $nodeTree) {
            $flatten = $this->flat($nodeTree);
            /**
             * Validar se total de elementos viola restrição de limite
             */
            $lineCount = count($flatten);
            if ($validateInput && $lineCount > $this->setup->getMaxItems()) {
                throw new LimitViolationException("Maximum items violation. Datasource has {$lineCount} elements per line, but only {$this->setup->getMaxItems()} were allowed.");
            }
            /**
             * Incluir campos extra do modelo, para esta item criado
             */
            if (!empty($this->registeredExtras)) {
                $flatten = $this->evalueteExtras($flatten);
            }
            //Seta resultado de modelo intermediário como a coleção de dados compilada a partir da arvore binária
            $this->sourceMap[] = $flatten;
        }
    }

    /**
     * Gerar uma array unidimensional para armazenar qualquer elemento lido da fonte de dados para um elemento pivô
     *
     * @param Node $nodeTree Node tree para extração de propriedades relacionadas
     *
     * @return array
     */
    private function flat(Node $nodeTree): array
    {
        /**
         * Colocando todos os elementos filhos em um array unidimensional
         */
        $flatten = $this->flatChildren($nodeTree);
        /**
         * Incluindo os elementos vizinhos no mesmo array
         */
        foreach ($nodeTree->getNeighbors() as $neighbor) {
            $flatten = array_merge(
                $flatten,
                // Também são elementos relacionados, todos os filhos do elementos vizinhos
                $this->flatChildren($neighbor)
            );
        }
        /**
         * Incluindo os elementos ancestrais no mesmo array
         */
        if ($nodeTree->getParent() && $nodeTree->getParent() instanceof Node) {
            $flatten = array_merge(
                $flatten,
                //Inclui elementos relacionsdos a qualquer ancestral na hierarquia
                $this->flatParent($nodeTree->getParent())
            );
        }
        return $flatten;
    }

    /**
     * Gerar uma matriz unidimensional para armazenar qualquer elemento lido da fonte de dados
     * Método percorre apenas eleentos ancestrais do node pivo
     *
     * @param Node $nodeTree Node tree para extração de propriedades relacionadas
     *
     * @return array
     */
    private function flatParent(Node $nodeTree): array
    {
        $elements = [];
        foreach ($nodeTree->getChildren() as $child) {
            /**
             * Se o elemento for uma folha e seu valor não for nulo, adicione ao dicionário
             */
            if (
                $child->isLeaf() &&
                $child->getValue() !== null &&
                !is_string($child->getValue())
            ) {
                $elements[$child->getValue()->getElement()] = $child->getValue();
            }
        }
        /**
         * Se o elemento ainda não for o elemento root, continuar lendo elemntos ancestrais
         */
        if ($nodeTree->getParent()) {
            $elements = array_merge(
                $elements,
                $this->flatParent($nodeTree->getParent(), false)
            );
        }
        return $elements;
    }

    /**
     * Gerar uma matriz unidimensional para armazenar qualquer elemento lido da fonte de dados
     * Método percorre apenas eleentos filhos do node pivo
     *
     * @param Node $nodeTree Node tree para extração de propriedades relacionadas
     *
     * @return array
     */
    private function flatChildren(Node $nodeTree): array
    {
        $elements = [];
        foreach ($nodeTree->getChildren() as $child) {
            if ($child->isLeaf()) {
                /**
                 * Se o elemento for uma folha e seu valor não for nulo, adicione ao dicionário
                 */
                if ($child->getValue() !== null && !is_string($child->getValue())) {
                    $elements[$child->getValue()->getElement()] = $child->getValue();
                }
            } else {
                /**
                 * Se o elemento não for uma folha, então ele tem filhos para ler
                 */
                $elements = array_merge(
                    $elements,
                    $this->flatChildren($child)
                );
            }
        }
        return $elements;
    }

    /**
     * Criar uma arvore binária para virtualizar a estrutura do arquivo de entrada relacionando os atributos relacionados a cada elemento pivo
     *
     * @param array $sourceData Dados de entreda, como array, cada registro em uma linha
     * @param bool|null $check Inidicador para validar ou não dados de entrada durante a extração
     * @param array $parentNodeKey Nome do elemento pai
     *
     * @return Node
     */
    // private function buildTreeFromSource(array $sourceData, ?bool $check = null, string|int $parentNodeKey = 'root'): Node
    private function buildTreeFromSource(array $sourceData, ?bool $check = null, array $parentNodeKey = ['root']): Node
    {
        $root = new Node($parentNodeKey[count($parentNodeKey) - 1]);
        $checkFieldLength = $this->setup->getLength();
        $validateInput = ($check === null && $this->setup->isCheckInput()) || $check === true;
        /**
         * Percorrendo cada item da fonte de dados (ou objeto aninhado, nas chamadas recursivas)
         */
        $isList = array_is_list($sourceData);
        foreach ($sourceData as $key => $value) {
            /**
             * Se o item for um array, ainde deve ser tratado como um objeto complexo, passando novamente para a função cada item recursivamente
             */
            if (is_array($value)) {
                $nodeKey = $parentNodeKey;
                //Listas sequenciais não devem incluir indice
                if (!$isList) {
                    $nodeKey[] = $key;
                }
                $childNode = $this->buildTreeFromSource($value, $validateInput,  $nodeKey);
                /**
                 * Se for o elemento pivô, salva uma referência para construir um elemento de saída
                 */
                if ($key == $this->setup->getPivot()) {
                    $this->elementList[] = $childNode;
                }
                //Adiciona node à estrutura hierarquica
                $root->addChild($childNode);
            } else {
                /**
                 * Se não for um array, este item é um elemento 
                 * Percorrer a lista de incidencias dele no modelo para a extração correta dos dados
                 */
                $fullPath = $parentNodeKey;
                array_shift($fullPath);
                $fullPath[] = $key;
                $keyPath = str_replace('.', '.elements.', implode('.', $fullPath));
                if (Arr::has($this->model, $keyPath)) {
                    //Recuperar definição de elemento
                    $elementDefinition = Arr::get($this->model, $keyPath);
                    //Preencher com valor da fonte de dados
                    $elementDefinition['value'] = (string)($value ?? $this->setup->getDefault() ?? '');
                    /**
                     * Validar o tamanho limite de cada campo
                     */
                    if (
                        $validateInput &&
                        $checkFieldLength != null &&
                        $checkFieldLength < mb_strlen($elementDefinition['value'])
                    ) {
                        throw new LimitViolationException("Maximum length violation in field {$elementDefinition['element']}. Datasource has " . mb_strlen($elementDefinition['value']) . " bytes, but only {$checkFieldLength} were allowed.");
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
                        //     echo "{$e->getMessage()} - {$e->getLine()}\n";
                        // }
                    }
                    /**
                     * Se estiver tudo certo, criar um nobo node com o valor do elemento e adicionar à arvore
                     */
                    $childNode = new Node($key);
                    $childNode->setValue($element);
                    $root->addChild($childNode);
                }
            }
        }
        /**
         * Sempre retorna o node criado para que as chamas recursivas possan relacionar os itens na hierarquia
         */
        return $root;
    }

    /**
     * Gerar todos os elementos de um modelo e os caminhos para acessar eles
     *
     * @param array $model Array de modelo
     * @param array $elPath Caminho do elemento
     * @param array $path Indica se deve construir estrutura de indices simplificada ou não
     *
     * @return void
     */
    protected function virtualizeModel(array $model, array $elPath = [], bool $simple = true): void
    {
        /**
         * Percorrendo todas as propriedades do modelo de entrada
         */
        foreach ($model as $k => $vl) {
            $elPathInt = $elPath;
            /**
             * Se propriedade do modelo for um array e tiver um atributo 'elements', este ainda é um objeto complexo
             * Passar recursivamente para a funcção até que os elementos sejam simples
             */
            if (
                is_array($vl) &&
                isset($vl['elements'])
            ) {
                $elPathInt[] = $k;
                $this->virtualizeModel($vl['elements'], $elPathInt, $simple);
            } else if (isset($vl['element'])) {
                //Inlcuir chave como parte do caminho do elemento
                $elPathInt[] = $k;
                //Se ainda não definida esta chave no modelo virtual, inicializa-la vazia
                if (!isset($this->virtualModel[$vl['element']])) {
                    $this->virtualModel[$vl['element']] = [];
                }
                //Se estiver compilando o indice simplificado, inserir apenas o caminho dentro do array
                if ($simple) {
                    $this->virtualModel[$vl['element']][] = implode('.', $elPathInt);
                } else {
                    /**
                     * Se não for o indice simplificado, serão incluidas as propriedades
                     * el => Caminho do elemento no modelo
                     * path => Caminho do elemento no objeto de saida
                     * parent => Caminho para o pai do elemento no modelo
                     *
                     */
                    $elementModel = implode('.elements.', $elPathInt);
                    $elementPath = implode('.', $elPathInt);
                    array_pop($elPathInt);
                    $elementParent = implode('.elements.', $elPathInt);
                    $this->virtualModel[$vl['element']][] = [
                        'el' => $elementModel,
                        'path' => $elementPath,
                        'parent' => $elementParent
                    ];
                }
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
    public function build(Base|array $source): void
    {
        /**
         * Criar modelo virtualizado de caminhos de cada elemento, usando a estrutura mais complexa que indica caminhos na fonte de dados e no modelo
         */
        $this->virtualizeModel($this->model, [], false);
        $dataOut = [];
        //Percorrer cada item da lista de elementos recebidos
        $inputData = $source;
        if ($source instanceof Base) {
            $inputData = $source->sourceMap;
        }
        foreach ($inputData as $register) {
            $objectDestination = [];
            //Percorrer cada restro de cada item
            foreach ($register as $field) {
                //Se o elemento não estiver definido no modelo, apenas ignorar ele
                if (!isset($this->virtualModel[$field->getElement()])) {
                    continue;
                }
                /**
                 * Para cada ocorrencias do elemento no modelo
                 */
                foreach ($this->virtualModel[$field->getElement()] as $key) {
                    /**
                     * Tentar ler a definiaõ do campo por ordem de prioridade, cond=siderando primeiro a chave de objeto e depois o nome de elemento
                     */
                    $elementDefinition = Arr::get(
                        $this->model,
                        $key['el'],
                        Arr::get(
                            $this->model,
                            $field->getElement(),
                            false
                        )
                    );
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
                        Arr::set(
                            $objectDestination,
                            $key['path'],
                            $typeData->getValue()->__toString()
                        );
                    }
                }
            }
            //Adicionar propriedade ao objeto
            $dataOut[] = $this->addToOutput($objectDestination);
        }
        /**
         * Compila dados de saida em uma string JSON 
         */
        $this->built = json_encode(Base::convertToUtf8Recursive($dataOut));
    }

    /**
     * Retorna o proprio campo
     * Método criado para evitar a necessidade de clonagem do método build em Xml
     *
     * @param array $objectDestination Dados de saida
     *
     * @return array
     */
    private function addToOutput(array $objectDestination): array
    {
        return $objectDestination;
    }

    /**
     * Devolver o valor de elementList
     *
     * @return  array
     */
    public function getElementList()
    {
        return $this->elementList;
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

        foreach ($model['definitions'] as $definitions) {
            $checkDef = $this->checkDefinition($definitions);
            if ($checkDef['status'] == 0) {
                return $checkDef;
            }
        }
        return [
            'status' => true,
            'message' => 'OK'
        ];
    }

    private function checkDefinition(array $definitions): array
    {
        foreach ($definitions['elements'] ?? [] as $definition) {
            if (isset($definition['type'])) {
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
            } elseif (isset($definition['elements'])) {
                return $this->checkDefinition($definition);
            }
        }
        return [
            'status' => true,
            'message' => 'OK'
        ];
    }
}
