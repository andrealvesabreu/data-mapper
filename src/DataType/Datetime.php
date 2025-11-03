<?php

declare(strict_types=1);

namespace Inspire\DataMapper\DataType;

use Inspire\DataMapper\Exceptions\ValueException;

/**
 * Classe para elementos que contenham data, hora ou ambos
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
final class Datetime extends Type implements TypeInterface
{

    /**
     * Propriedades aplicáveis a este tipo de dado
     */
    const PROPERTIES = [
        'element' => [
            'type' => 'string',
            'required' => true
        ],
        'minimum' => [
            'type' => 'datetime',
            'default' => null
        ],
        'maximum' => [
            'type' => 'datetime',
            'default' => null
        ],
        'minLength' => [
            'type' => 'int',
            'default' => null,
            'minimum' => 0,
            'maximum' => 4096
        ],
        'maxLength' => [
            'type' => 'int',
            'default' => null,
            'minimum' => 0,
            'maximum' => 4096
        ],
        'default' => [
            'type' => 'string',
            'default' => null
        ],
        'required' => [
            'type' => 'bool'
        ],
        'nullable' => [
            'type' => 'bool'
        ],
        'format' => [
            'type' => 'string',
            'default' => null
        ]
    ];

    /**
     * Foramto de string para formatar a saída ou para extrair o valor de entrada
     * Será a referência para adicionar/remover máscaras, obter datas válidas e converter para outros formatos
     *
     * @var string
     */
    protected string $format;

    /**
     * Construtor da classe. Sempre receberá os dados para definir os valores de suas propriedades
     *
     * @param array $props Array com valores de propriedades
     * @param bool $fromSource Indica se os dados estão vindo de uma fonte de dados ou se recebeu os dados estraidos de uma fonte anteriormente
     */
    public function __construct(array $props, bool $fromSource = true)
    {
        if (isset($props['format'])) {
            $this->format = $props['format'];
        } else {
            $this->format = 'yyyy-mm-dd';
        }

        //Classe pai tratará as propriedades padrão
        parent::__construct($props, $fromSource);

        /**
         * Bloco para sefinir valor com base em dados de datasource
         */
        if ($fromSource) {
            //Se nenhum valor for definido, atribuir valor padrão. Caso não haja um valor padrão, considerar null
            if (
                !isset($props['value']) ||
                $props['value'] === null
            ) {
                $props['value'] = $props['default'] ?? null;
            }
            $this->value->setValues(
                (string)$this->getDate((string)$props['value']),
                (string)$props['value']
            );
        } //Bloco para definir dados com base em dados coletados de outro modelo anteriormente
        else {
            if (
                !isset($props['value']) ||
                $props['value'] === null
            ) {
                $props['value'] = $props['default'] ?? null;
            }
            if ($this->format === null) {
                $this->format = 'yyyy-mm-dd hh:ii:ss';
            }
            $this->format = str_replace(
                ['yyyy', 'yy', 'mm', 'dd', 'hh', 'ii', 'ss'],
                ['Y', 'y', 'm', 'd', 'H', 'i', 's'],
                $this->format
            );
            if (!ctype_digit($props['value'])) {
                $props['value'] = strtotime($props['value']);
            }
            $value = date($this->format, intval($props['value'] ?? $props['default'] ?? '0'));
            $this->value->setValues(
                $props['value'],
                $value
            );
        }
    }

    /**
     * Devolve UNIX time da data informada
     *
     * @param string $value Data para ser processada
     * @return int|null
     */
    protected function getDate(string $value): ?int
    {
        /**
         * Format allow:
         * d (dd) day of the onth
         * y (yy or yyyy) year two or four digits
         * m (mm) month as number
         * h (hh) hour, as number
         * i (ii) minute, as number
         * s (ss) seconds, as number
         * M (MMM) month name
         */
        $parts = [
            'd' => [],
            'm' => [],
            'y' => [],
            'h' => [],
            'i' => [],
            's' => [],
            'M' => []
        ];
        $arrMask = str_split($this->format);
        $arrVal = str_split($value);
        for ($a = 0; $a < min(
            count($arrMask),
            count($arrVal)
        ); $a++) {
            $charVal = $arrVal[$a];
            $charMask = $arrMask[$a];
            //Mask changed
            if (array_key_exists($charMask, $parts)) {
                $parts[$charMask][] = $charVal;
            }
        }
        //Tratar ano com 2 digitos, assumindo que estes nunca poderão ser posteriores à 2039
        $parts['y'] = implode('', $parts['y']);
        if (strlen($parts['y']) == 2) {
            if (intval($parts['y'] < 40)) {
                $parts['y'] = "20{$parts['y']}";
            } else {
                $parts['y'] = "19{$parts['y']}";
            }
        }
        $year = str_pad($parts['y'], 4,  '0', STR_PAD_LEFT);
        $month = str_pad(implode($parts['m'] ?? []), 2,  '0', STR_PAD_LEFT);
        $day = str_pad(implode($parts['d'] ?? []), 2,  '0', STR_PAD_LEFT);
        $hour = str_pad(implode($parts['h'] ?? []), 2,  '0', STR_PAD_LEFT);
        $minute = str_pad(implode($parts['i'] ?? []), 2,  '0', STR_PAD_LEFT);
        $second = str_pad(implode($parts['s'] ?? []), 2,  '0', STR_PAD_LEFT);
        //Calcula timestamp da data
        return strtotime("{$year}-{$month}-{$day} {$hour}:{$minute}:{$second}");
    }

    /**
     * Valida se a data informada está no intervalo delimitado, quando houver delimitação de período
     *
     * @return bool
     */
    public function validateRange(): bool
    {
        if (
            $this->value !== null &&
            $this->range !== null &&
            $this->value->getValue() !== null
        ) {
            /**
             * Restição de valor mínimo
             */
            if (
                $this->range->getMinimum() !== null &&
                $this->value->getValue() < $this->range->getMinimum()
            ) {
                $min = date('Y-m-d H:i:s', $this->range->getMinimum());
                throw new ValueException("Constraint violation: The field '{$this->element}' contains a value less than a minimum value of {$min}.");
            }
            /**
             * Restrição de valor máximo
             */
            else if (
                $this->range->getMaximum() !== null &&
                $this->value->getValue() > $this->range->getMaximum()
            ) {
                $max = date('Y-m-d H:i:s', $this->range->getMaximum());
                throw new ValueException("Constraint violation: The field '{$this->element}' contains a value greatest than a maximum value of {$max}.");
            }
        } else if (!$this->nullable) {
            /**
             * Restrição de valor nulo
             */
            throw new ValueException("Constraint violation: Non nullable field '{$this->element}' has a null value.");
        }
        return true;
    }
}
