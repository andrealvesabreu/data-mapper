<?php

declare(strict_types=1);

namespace Inspire\DataMapper\Mappers;

use Closure;
use Illuminate\Support\Arr;
use Inspire\DataMapper\DataType\Type;
use Inspire\DataMapper\Exceptions\LimitViolationException;
use Inspire\DataMapper\Mappers\Setup\{
    Main,
    Proceda as SetupProceda
};
use Tree\Node\Node;

/**
 * Classe para extração ou compilação de dados em formato de PROCEDA/TIVIT
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
final class Proceda extends Base implements MapperInterface
{

    /**
     * Objeto de confguração
     *
     * @var SetupProceda
     */
    protected SetupProceda $setup;

    /**
     * @var array
     */
    private array $indexes = [];

    /**
     * Keep a pointer to last register for each type
     *
     * @var array
     */
    private array $registerPointer = [];

    /**
     * Keep a pointer for each pivot element to build a complete register after mapping is complete
     *
     * @var array
     */
    private array $pivotPointer = [];

    /**
     * Make a virtual model index, setting paths for each element
     *
     * @var array
     */
    private array $virtualModel = [];

    /**
     * Make a virtual model index, setting paths for each element
     *
     * @var array
     */
    private array $originalModel = [];

    /**
     *
     * @var array
     */
    private array $builtRegistersIndex = [];

    /**
     * Registrar tamanho limite de linha para cada registro
     * @var array
     */
    private array $registerSize = [];

    /**
     * Construtor de classe
     *
     * @param array $model Modelo de dados esperado como datasource
     * @param SetupProceda $setup Objeto de configuração
     */
    public function __construct(array $model, SetupProceda $setup)
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
         * Extrair todos os caminhos de cada elemento no datasource, criando um mindice
         */
        $this->virtualizeModel($this->model);
        /**
         * Monta uma arvore binária para organizar os elementos do datasource conforme hierarquia de elementos
         */
        $this->buildTree($source, $check);
        //Limpa qualquer possível informação prévia de uma outra leitura anterior
        $this->sourceMap = [];
        //Valida limites
        if (($check === null && $this->setup->isCheckInput()) || $check === true) {
            $this->validateMinMax($this->pivotPointer[0]->root());
        }
        /**
         * Compilar registros para cada elemento pivô identificado
         */
        foreach ($this->pivotPointer as $nodeTree) {
            /**
             * Coloca todos os elementos relacionados em um array unidimensional
             */
            $flatten = $this->flat($nodeTree);
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
    public function flat(Node $nodeTree)
    {
        $flatten = [];
        /**
         * Incluir os elementos filhos
         */
        foreach ($nodeTree->getChildren() as $child) {
            if ($child->isLeaf()) {
                if ($child->getValue() !== null) {
                    $flatten[$child->getValue()->getElement()] = $child->getValue();
                }
            }
        }
        /**
         * Incluir ancestrais
         */
        if ($child->getParent()) {
            $flatten = array_merge(
                $this->buildStructure($child->getParent()), //Elementos de nível superior primeiro
                $flatten //Dados específicos do registro
            );
        }
        return $flatten;
    }

    /**
     * Copila dados de todas as propriedades que compoem um registro
     *
     * @param Node $node Node tree para extração de propriedades relacionadas
     *
     * @return array
     */
    private function buildStructure(Node $node): array
    {
        $elements = [];
        /**
         * Inerir todos os elementos ancestrais primeiro
         */
        if ($node->getParent()) {
            $elements = array_merge(
                $elements,
                $this->buildStructure($node->getParent())
            );
        }
        /**
         * Adicionar elementos que são folhas neste nó, o que significa que suas propriedades deste registro
         */
        foreach ($node->getChildren() as $child) {
            if ($child->isLeaf()) {
                $elements[$child->getValue()->getElement()] = $child->getValue();
            }
        }
        return $elements;
    }

    /**
     * Extrair dados de datasource para uma arvore binária
     *
     * @param array $source Fonte de dados
     * @param bool $check Indicador para verificar ou não error na extração
     *
     * @return void
     */
    private function buildTree(array $source, ?bool $check = null): void
    {
        /**
         * Percorrendo cada linha da fonte de dados
         */
        foreach ($source as $line) {
            /**
             * Executar a função find de cada elemento do modelo até encontrar aquele que identifica o registro atual
             * Elementos de um modelo que não tenham a função find nunca serão utilizados
             */
            foreach ($this->model as $registerName => $itens) {
                if (
                    isset($itens['find']) &&
                    $itens['find'] instanceof Closure &&
                    $itens['find']($line)
                ) {
                    /**
                     * Executar parser da linha, utilizando como referencia a definição de elementos do modelo que identificou o registro
                     */
                    $this->parseLine($line, $registerName,  $itens['elements'], $check);
                    break;
                }
            }
        }
    }

    /**
     * Extrair dados da linha de acordo com a definição do modelo
     *
     * @param string $line String contendo uma linha do arquivo de entrada
     * @param string $registerName Nome do registro que esta linha representa
     * @param array $itens Itens previstos no modelo para este registro
     * @param bool|null $check Indicador para verificar ou não error na extração
     *
     * @return void
     */
    private function parseLine(string $line, string $registerName, array $itens, ?bool $check = null): void
    {
        $validateInput = ($check === null && $this->setup->isCheckInput()) || $check === true;
        /**
         * Criar um elemento identificador de registro para fazer referencia ao elemento do modelo
         */
        $mainLineLement = $this->makeElementRegId($registerName);
        //Validar elemento, se necessário
        if ($mainLineLement && $this->setup->isCheckInput()) {
            $mainLineLement->validate();
        }
        /**
         * Até que se verifique o contrario, considerar que este não é o registro pivo
         */
        $isPivotInLine = false;
        /**
         * Inicializa um node da tree para incluir as propriedades do registro, e permitir relacionar ela com nodes filhos e ancestrais depois
         */
        $lineNode = new Node($mainLineLement->getValue()->__toString());
        //Percorrer propriedadse definidas no modelo para esta linha
        foreach ($itens as $name => $item) {
            /**
             * Recuperar definição de elemento do modelo
             */
            $elementDefinition = $this->getValue($line, $item, $name, $registerName);
            //Valida se este é o elemento pivo do modelo
            $isPivotInLine = $isPivotInLine || $item['element'] == $this->setup->getPivot();
            /**
             * Cria um pbjeto Type usando esta definição
             */
            $element = Type::create($elementDefinition);
            //Validar valor de campo, se validação habilitada (desativar para proceda, pois ocampo já é extraido com tamanho exato)
            // if ($validateInput) {
            //     $element->validate();
            // }
            //Adicionar o elemento na tree
            if ($element !== null) {
                $leafeNode = new Node($element->getElement());
                $leafeNode->setValue($element);
                $lineNode->addChild($leafeNode);
            }
        }
        /**
         * Criar um ponteiro para o registro para facilitar o relacionamento posterior
         */
        $this->registerPointer[$mainLineLement->getValue()->__toString()] = &$lineNode;
        /**
         * Se o registro atual tem um registro pai no modelo; e 
         * Se já existe um ponteiro criado para este eleemnto
         * Inserir registro criado dentro do node pai na arvore
         */
        if (
            isset($this->model[$registerName]['parent']) &&
            isset($this->registerPointer[ltrim($this->model[$registerName]['parent'], 'R')])
        ) {
            $this->registerPointer[ltrim($this->model[$registerName]['parent'], 'R')]->addChild($lineNode);
        }
        /**
         * Se registro foi ide3ntiifcado como pivô, armazenar um ponteiro para compilação de dados depois
         */
        if ($isPivotInLine) {
            $this->pivotPointer[] = &$lineNode;
        }
    }

    /**
     * Extrair valor de fonte de dados usando definição de modelo como referencia
     *
     * @param string $line String contendo uma linha do arquivo de entrada
     * @param array $item Definição do item a ser extraido, conforme modelo
     * @param string $name Nome do elemento a ser extraido
     * @param string $registerID ID de registro que esta sendo lido
     *
     * @return array
     */
    private function getValue(string $line, array $item, string $name, string $registerID): array
    {
        $elementDefinition = $item;
        $value = substr($line, $item['start'] - 1, $item['maxLength']);
        //Definir propriedade filler, priorizando definição do elemento. Se não houver, usar da configuração
        if (isset($elementDefinition['filler'])) {
            $filler = $elementDefinition['filler'];
        } else {
            $filler = $this->setup->getFiller($elementDefinition['type']);
        }
        //Definir propriedade align, priorizando definição do elemento. Se não houver, usar da configuração
        if (isset($elementDefinition['align'])) {
            $align = $elementDefinition['align'];
        } else {
            $align = $this->setup->getAlign($elementDefinition['type']);
        }
        /**
         * Remover filler no valor do campo
         */
        switch ($align) {
            case STR_PAD_LEFT:
                $value = ltrim($value, $filler);
                break;
            case STR_PAD_RIGHT:
                $value = rtrim($value, $filler);
                break;
            case STR_PAD_BOTH:
                $value = trim($value, $filler);
                break;
        }
        //Se nenhum valor foi definido, mas o modelo tem um valor default, utilizá-lo
        if (
            empty($value) &&
            !empty($elementDefinition['default'])
        ) {
            $value = $elementDefinition['default'];
        }
        //Se valor vazio, mas o tupo de dados for numérico (inf ou float), considerar 0
        if (in_array($elementDefinition['type'], ['float', 'integer']) && empty($value)) {
            $value = '0';
        }
        //Definir valor do elemento
        $elementDefinition['value'] = $value;
        /**
         * Registra caminho de dado no modelo de origem
         */
        $elementDefinition['path'] = "{$registerID}.{$name}";
        return $elementDefinition;
    }

    /**
     * Cria um elemento identificador de registro
     *
     * @param string $registry String de identificação de registro estraida do modelo.
     *
     * @return Type
     */
    private function makeElementRegId(string $registry): Type
    {
        $registry = ltrim($registry, 'R');
        $element = Type::create([
            'value' => $registry,
            'filler' => ' ',
            'default' => '',
            'start' => 1,
            'type' => 'string',
            'element' => $registry,
            'path' => $registry,
            'minLength' => strlen($registry),
            'maxLength' => strlen($registry)
        ]);
        return $element;
    }

    /**
     * Gerar todos os elementos de um modelo e os caminhos para acessar eles
     *
     * @return void
     */
    protected function virtualizeModel(): void
    {
        /**
         * Listar todos os itens que vão para cada registro no modelo para identificar facilmente quais registros extrair das fontes
         */
        $this->virtualModel = [];
        foreach ($this->model as $registry => $data) {
            foreach ($data['elements'] as $element) {
                //Se ainda não definida esta chave no modelo virtual, inicializa-la vazia
                if (!isset($this->virtualModel[$element['element']])) {
                    $this->virtualModel[$element['element']] = [];
                }
                // Inserir identifiecador do elemento no array
                $this->virtualModel[$element['element']][] = $registry;
            }
            /**
             * Indexes recebe apenas a definição de elementos de cada registro
             */
            $this->indexes[$registry] = array_combine(
                array_column(
                    $this->model[$registry]['elements'],
                    'element'
                ),
                array_keys(
                    $this->model[$registry]['elements']
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
    public function build(Base|array $source): void
    {
        $modelOutput = [];
        /**
         * Manter copia de modelo original e limpar dados de indices definidos anteriormente
         */
        $this->originalModel = $this->model;
        $this->indexes = [];
        foreach ($this->model as $reg => $item) {
            $last = end($item['elements']);
            $this->registerSize[$reg] = $last['start'] + $last['maxLength'] - 1;
        }

        $paths = [];
        foreach ($this->model as $key => &$registry) {
            $this->indexes[$key] = $key;
            //Remover funções find do modelo
            unset($registry['find']);
            //Montar todo a hierarquia do elemento até aqui, olhando pelo modelo os campos parent
            $path = [];
            $cpRegistruy = $registry;
            while (isset($cpRegistruy['parent'])) {
                $path[] = 'elements';
                $path[] = $cpRegistruy['parent'];
                $cpRegistruy = $this->model[$cpRegistruy['parent']];
            }
            /**
             * Salva referencia de caminho de esta cada elemento no modelo
             */
            $paths[$key] = implode('.', array_reverse($path));
        }
        /**
         * Monta novo modelo virtual, agora com hierarquia de elementos como JSON/XML e atualiza o modelo de entrada com base nele
         */
        foreach ($paths as $reg => $value) {
            if (!empty($value)) {
                $value .= '.';
            }
            Arr::set(
                $modelOutput,
                "{$value}{$reg}",
                $this->model[$reg]
            );
        }
        $this->model = $modelOutput;

        /**
         * Criar modelo virtual com caminho de elementos para extraçãos simplificada
         */
        $this->virtualizeModelBuild($this->model);

        $dataOut = [];
        //Percorrer cada registro
        $inputData = $source;
        if ($source instanceof Base) {
            $inputData = $source->sourceMap;
        }
        foreach ($source->sourceMap as $register) {
            $objectDestination = [];
            //Percorrer cada campo do registro
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
                            $typeData
                        );
                    }
                }
            }
            //Adicionar registro para saida
            $dataOut[] = $objectDestination;
        }
        /**
         * Aqui temos todos os elementos construídos em $dataOut para construir o arquivo
         */
        $output = [];
        foreach ($dataOut as $item) {
            $output = array_merge($output, $this->buildRegisters($item));
        }
        /**
         * Compila dados de saida em uma string JSON 
         */
        $this->built = json_encode(Base::convertToUtf8Recursive($output));
    }

    /**
     * Criar modelo virtual com base na hierarquia definida no modelo
     *
     * @param array $model Definições de modelo de dados
     * @param array $elPath Caminho do elemeno
     *
     * @return void
     */
    private function virtualizeModelBuild(array $model, array $elPath = []): void
    {
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
                $this->virtualizeModelBuild($vl['elements'], $elPathInt);
            } else if (isset($vl['element'])) {
                //Inlcuir chave como parte do caminho do elemento
                $elPathInt[] = $k;
                //Se ainda não definida esta chave no modelo virtual, inicializa-la vazia
                if (!isset($this->virtualModel[$vl['element']])) {
                    $this->virtualModel[$vl['element']] = [];
                }
                /**
                 * el => Caminho do elemento no modelo
                 * path => Caminho do elemento no objeto de saida
                 * parent => Caminho para o pai do elemento no modelo
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

    /**
     * Compilar registro
     *
     * @param array $items Propriedades para compilação de registro
     *
     * @return array
     */
    private function buildRegisters(array $items, ?string $idx = null): array
    {
        $lineData = [];
        $nested = [];
        $output = [];
        /**
         * Dividir os dados entre os elementos com valor definido e os objetos relacionados ao registro
         */
        foreach ($items as $key => $item) {
            if (is_array($item)) {
                $nested[] = $key;
            } else {
                $lineData[] = $item;
            }
        }
        /**
         * Se receber um ID de registro
         */
        if ($idx !== null) {
            //Remove item de ID e ordena elementos por valor de posição inicial
            array_unshift($lineData, $this->makeElementRegId($idx));
            usort($lineData, function ($a, $b) {
                return $a->getPosition()->getStart() > $b->getPosition()->getStart() ? 1 : -1;
            });
            // var_dump($this->model["R{$lineData[0]->getValue()}"]);
            /**
             * Compila linha com os dados coletados
             */
            $builtLine = $this->buildLine($lineData);
            //Se não houver erros na compilação
            if (
                $builtLine['status'] &&
                !empty($builtLine['set']) &&
                $builtLine['data'] !== null
            ) {
                //Caso ainda não tenha sido registrado nenhum registro deste tipo, inicializar vazio
                if (!isset($this->builtRegistersIndex[$builtLine['register']])) {
                    $this->builtRegistersIndex[$builtLine['register']] = [];
                }
                /**
                 * Validar restrições de chave se a inclusão for necessária
                 * Neste modelo, é possivel indicar os elementos de um registro que são chaves.
                 * Caso este registro tenha o mesmo valor de chave do item anterior, não é neceaaário reinseri-lo
                 */
                $dataModel = $this->originalModel["R{$builtLine['register']}"];
                $checkKey = $dataModel['key'] ?? [];
                $addRegister = true;
                //Total de ocorrencias deste registro. para facilitar a identificação do ultimo registro na lista
                $totalRegisterAlredySet = count($this->builtRegistersIndex[$builtLine['register']]);
                //Se a chave está presente no registro, validar
                if (!empty($checkKey)) {
                    /**
                     * Verificando se é igual ao registro anterior, comparando todos os campos que compoem a chave
                     */
                    $isItTheSame = true;
                    if (!empty($this->builtRegistersIndex[$builtLine['register']])) {
                        $lastOne = $this->builtRegistersIndex[$builtLine['register']][$totalRegisterAlredySet - 1];
                        foreach ($checkKey as $key) {
                            $isItTheSame = $isItTheSame &&
                                isset($builtLine['set'][$key]) &&
                                isset($lastOne[$key]) &&
                                $lastOne[$key]->getValue()->__toString() == $builtLine['set'][$key]->getValue()->__toString();
                        }
                        $addRegister = !$isItTheSame;
                    }
                }
                //Se nãp for verificadaa nenhuma restrição que indicque o contrario, inserir o registro
                if ($addRegister) {
                    /**
                     * Checking for register count limits
                     */
                    // if (
                    //     isset($dataModel['maxItems']) &&
                    //     $dataModel['maxItems'] == $totalRegisterAlredySet
                    // ) {
                    //     throw new LimitViolationException("Yout cant include more than {$dataModel['maxItems']} of register {$builtLine['register']}");
                    // }
                    $output[] = $builtLine['data'];
                }
                /**
                 * Atualiza dados da ultima linha inserida para este tipo de registro
                 */
                $this->builtRegistersIndex[$builtLine['register']][] = $builtLine['set'];
            }
        }
        /**
         * Ordenar campos de nested com base no modelo
         * Este grupo indica elementos filhes do registro anterior, e precisam ser ordenados pelo modelo pois nem sempre os identiifcadores de registro são em sequencia numérica ascendente
         */
        $nested = $this->sortArrayByReference($nested, $this->indexes);
        foreach ($nested as $key) {
            $item = $items[$key];
            //Se houver itens deste tipo de registro, passar dados para a função compilar a linha dele recursivamente
            if (is_array($item)) {
                $output = array_merge($output, $this->buildRegisters($item, $key));
            }
        }
        return $output;
    }

    /**
     * Ordenar um array por chaves utilizando outro como referencia
     *
     * @param array $arrayToSort Array que deverá ser ordenado
     * @param array $referenceArray Array de modelo para ordenação
     *
     * @return array
     */
    private function sortArrayByReference(array $arrayToSort, array $referenceArray): array
    {
        $referenceOrder = array_flip(array_keys($referenceArray));
        usort($arrayToSort, function ($a, $b) use ($referenceOrder) {
            $posA = $referenceOrder[$a] ?? PHP_INT_MAX;
            $posB = $referenceOrder[$b] ?? PHP_INT_MAX;
            if ($posA == $posB) {
                return 0;
            }
            return ($posA < $posB) ? -1 : 1;
        });
        return $arrayToSort;
    }

    /**
     * Compilar uma linha usando o array de elementos
     *
     * @param array $lineData Array com os dados que serão usados na criação do texto da linha
     *
     * @return array
     */
    private function buildLine(array $lineData): array
    {
        $line = [];
        $registerId = null;
        $expectStart = 1;
        $elementsSet = [];
        /**
         * Para cada item, definir valor usando especificações do modelo
         */
        foreach ($lineData as $dt) {
            /**
             * Se campo iniciar em posição posterior ao esperado, inserir espações vazios no espaço que não tem valores definidos
             */
            if ($expectStart < $dt->getPosition()->getStart()) {
                $missingDataLen = $dt->getPosition()->getStart() - $expectStart;
                $line[] = str_pad(
                    '',
                    $missingDataLen,
                    $this->setup->getFiller('string'),
                    STR_PAD_LEFT
                );
            }
            //Inidica elementos que foram inseridos
            $elementsSet[$dt->getElement()] = $dt;
            //Identifica o id de registro. Será sempre atribuido na primeira iteração
            $registerId = $registerId ?? $dt->getElement();
            //Adiciona o campo aos dados da linha
            $line[] = $this->buildField($dt);
            //Indica onde é esperado que o próximo campo inicie
            $expectStart = $dt->getPosition()->getStart() + $dt->getSize()->getMinimum();
        }
        /**
         * Recuperar lista de elementos obrigatórios, conforme definição de modelo
         */
        $requiredFields = $this->getRequiredElements($this->originalModel["R{$registerId}"]['elements']);
        /**
         * Se todos os elementos obrigatórios estiverem presentes
         */
        if (count(
            array_intersect(
                array_map(function ($el) {
                    return $el->getElement();
                }, $elementsSet),
                $requiredFields
            )
        ) == count($requiredFields)) {
            return [
                'data' => str_pad(implode('', $line), $this->registerSize["R{$line[0]}"], ' ', STR_PAD_RIGHT),
                'register' => $registerId,
                'set' => $elementsSet,
                'status' => true
            ];
        }
        //Em caso de erros
        return [
            'data' => null,
            'status' => false,
            'register' => $registerId,
            'set' => $elementsSet,
            'error' => 'Missing required fields'
        ];
    }

    /**
     * Compilar valor de campo com base em especificação de modelo enviada
     *
     * @param Type $item Representa um campo que será convertido em texto conforme especifições de susas propriedades
     *
     * @return string
     */
    private function buildField(Type $item): string
    {
        $value = $item->getValue()->__toString();
        $type = strtolower((new \ReflectionClass($item))->getShortName());
        $filler = $item->getFiller() ?? $this->setup->getFiller($type);
        if (strlen($filler) == 0) {
            $filler = ' ';
        }
        $align = $item->getAlignment() ?? $this->setup->getAlign($type);
        return substr(str_pad($value, $item->getSize()->getMaximum(), $filler, $align), 0, $item->getSize()->getMaximum());
    }

    /**
     * Validar limites de campos por registro
     *
     * @param Node $node Node tree para extração de propriedades relacionadas
     *
     * @return int
     */
    private function validateMinMax(Node $node): int
    {
        $nodeCount = [
            $node->getValue() => 1
        ];
        foreach ($node->getNeighbors() as $child) {
            if ($child->isLeaf())
                continue;
            if (!isset($nodeCount[$child->getValue()])) {
                $nodeCount[$child->getValue()] = 0;
            }
            $nodeCount[$child->getValue()]++;
        }
        foreach ($nodeCount as $el => $ct) {
            $maxItems = $this->model["R{$el}"]['maxItems'] ?? 0;
            if ($ct > $maxItems) {
                throw new LimitViolationException("Register {$el} cannot be set more than {$maxItems}");
            }
        }
        foreach ($node->getChildren() as $child) {
            if ($child->isLeaf())
                continue;
            $this->validateMinMax($child);
        }
        return 0;
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
        $setup = [];
        foreach ($model['setup'] as $type => $value) {
            if ('align' == $type) {
                foreach ($value as $f => $v) {
                    $setup['align' . ucfirst($this->setup->getInternalType($f))] = $v;
                }
            } elseif ('filler' == $type) {
                foreach ($value as $f => $v) {
                    $setup['filler' . ucfirst($this->setup->getInternalType($f))] = $v;
                }
            } else {
                $setup[$type] = $value;
            }
        }
        $setup = $this->checkSetup($this->setup::PROPERTIES['setup'], $setup);
        if ($setup['status'] == 0) {
            return $setup;
        }

        foreach ($model['definitions'] as $definitions) {
            foreach ($definitions['elements'] as $definition) {
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
        }
        return [
            'status' => true,
            'message' => 'OK'
        ];
    }
}
