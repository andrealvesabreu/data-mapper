<?php

declare(strict_types=1);

namespace Inspire\DataMapper\Exceptions;

/**
 * Exceção para qualquer tipo de violação de limites
 *
 * @author André Alves <aalvesabreu.2010@gmail.com>
 * @since 1.0.0
 */
class LimitViolationException extends \Exception
{

    /**
     * @param mixed $message
     */
    public function __construct($message)
    {
        parent::__construct($message, 0, null);
    }
}
