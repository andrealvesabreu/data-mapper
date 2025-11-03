<?php

declare(strict_types=1);

namespace Inspire\DataMapper\Exceptions;

/**
 * Exceção para problemas de configuração
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
class SetupException extends \Exception
{

    /**
     * @param mixed $message
     */
    public function __construct($message)
    {
        parent::__construct($message, 0, null);
    }
}
