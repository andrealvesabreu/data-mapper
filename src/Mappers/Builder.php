<?php

declare(strict_types=1);

namespace Inspire\DataMapper\Mappers;

use Exception;
use Illuminate\Support\Arr;

/**
 * Classe para compilação de modelos
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
final class Builder
{
    /**
     * Elementos pré definidos
     * 
     * @var array
     */
    private array $elements = [];

    /**
     * Elementos definidos em modelo
     * 
     * @var array
     */
    private array $customElements = [];

    /**
     * Indicar se a coleção de elementos já foi compilada
     * 
     * @var bool
     */
    private bool $builtTypes = false;

    /**
     * Incluir definição de elemento
     * 
     * @param string $key Chave de identificação de elemento
     * @param array $definition Definição de elemento
     * @param array $namespaces Lista de namespaces aos quais esta definição se aplica
     * 
     * @return void
     */
    public function addElement(string $key, array $definition, array $namespaces = []): void
    {
        if (empty($namespaces)) {
            $namespaces = ['DEFAULT'];
        }
        foreach ($namespaces as $namespace) {
            Arr::set($this->elements, "{$namespace}.{$key}", $definition);
        }
        //Sinaliza como não compilado, pois a definição pode conter tipos dependentes
        $this->builtTypes = false;
    }

    /**
     * Mescla especificações de tipos definidos em modelos com especificações pré definidas de referencia
     * 
     * @param string $element
     * @param array $definition
     * 
     * @return void
     */
    public function mergeSpecs(string $element, array $definition): void
    {
        /**
         * Forçar compilação de tipos, se necessário
         */
        if ($this->builtTypes === false) {
            $this->buildTypes();
        }
        $definition['element'] = $element;
        list($namespace, $def) = array_pad(explode(':', $definition['model'] ?? ''), 2, null);
        if ($def == null) {
            $def = $namespace;
            $namespace = 'DEFAULT';
        }
        $this->customElements[$element] = array_merge(
            Arr::get($this->elements, "{$namespace}.{$def}", []),
            $definition
        );
        unset($this->customElements[$element]['model']);
    }

    /**
     * Compila dados de modelo, substituindo marcação de tipos pré-definidos por suas definiições
     * 
     * @param array $data
     * 
     * @return array
     */
    public function build(array $data): array
    {
        /**
         * Forçar compilação de tipos, se necessário
         */
        if ($this->builtTypes === false) {
            $this->buildTypes();
        }
        return $this->buildModel($data);
    }

    /**
     * Compila tipos
     *
     * @return void
     */
    private function buildTypes(): void
    {
        $pending = [];
        $updated = false;
        foreach ($this->elements as $namespace => $fields) {
            foreach ($fields as $element => $field) {
                if (strpos($field['type'], ':') !== false) {
                    if (Arr::has($this->elements, str_replace(':', '.', $field['type']))) {
                        $typeDef = Arr::get($this->elements, str_replace(':', '.', $field['type']));
                        /**
                         * Se o tipo dependente também precisa de outro elemento, assinalar para uma nova chamada da função
                         */
                        if (strpos($typeDef['type'], ':') !== false) {
                            $pending[] = "{$namespace}:{$element}";
                        } else {
                            $field['type'] = $typeDef['type'];
                            Arr::set(
                                $this->elements,
                                "{$namespace}.{$element}",
                                array_merge(
                                    $typeDef,
                                    $field
                                )
                            );
                            $updated = true;
                        }
                    }
                }
            }
        }
        /**
         * Se há indicação para uma nova chamada da função
         */
        if (!empty($pending)) {
            /**
             * Caso pelo menos um elemento tenha sido atualizado, existe possibilidade de resolução
             */
            if ($updated) {
                $this->buildTypes();
            } else {
                throw new Exception("Cannot build elements: " . implode(', ', $pending));
            }
        }
        //Indica que a compilação foi realizada
        $this->builtTypes = true;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function buildModel(array $data): array
    {
        $output = [];
        foreach ($data as $index => $element) {
            if (is_array($element)) {
                $output[$index] = $this->buildModel($element);
            } elseif (is_string($element) && str_starts_with($element, 'type:')) {
                list($type, $name, $namespace, $def) = array_pad(explode(':', $element), 4, null);
                if ($namespace == null || $def == null) {
                    $output[$index] = $this->customElements[$name];
                } else {
                    if ($def == null) {
                        $def = $namespace;
                        $namespace = 'DEFAULT';
                    }
                    $output[$index] = Arr::get($this->elements, "{$namespace}.{$def}", null);
                    $output[$index]['element'] = $name;
                }
            } else {
                $output[$index] = $element;
            }
        }
        return $output;
    }

    /**
     * Retornar saida de var_dump formatada com array de sintaxe curta com []
     * 
     * @param array $data
     * 
     * @return string
     */
    public function varExportShort(array $data): string
    {
        $dump = var_export($data, true);
        $dump = preg_replace('#(?:\A|\n)([ ]*)array \(#i', '[', $dump);
        $dump = preg_replace('#\n([ ]*)\),#', "\n$1],", $dump);
        $dump = preg_replace('#=> \[\n\s+\],\n#', "=> [],\n", $dump);

        if (gettype($data) == 'object') {
            $dump = str_replace('__set_state(array(', '__set_state([', $dump);
            $dump = preg_replace('#\)\)$#', "])", $dump);
        } else {
            $dump = preg_replace('#\)$#', "]", $dump);
        }
        return $dump;
    }

    /**
     * Valida estrutura de esquema 
     * 
     * @param array $schema
     * 
     * @return bool
     */
    public function checkSchema(array $schema): bool
    {
        /**
         * Criar parser de acordo com definição de modelo
         */
        $class = "\\Inspire\\DataMapper\\Mappers\\" . ucfirst($schema['parser']);
        $classSetup = "\\Inspire\\DataMapper\\Mappers\\Setup\\" . ucfirst($schema['parser']);
        $parser = new $class(
            $schema['definitions'],
            new $classSetup($schema['setup'])
        );
        $validate = $parser->checkSchema($schema);
        if ($validate['status'] == 0) {
            throw new \Exception($validate['message']);
        }
        return true;
    }
}
