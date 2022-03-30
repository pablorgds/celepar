<?php
/**
 * Created by PhpStorm.
 * User: roberson.faria
 * Date: 22/12/15
 * Time: 14:18
 */

namespace Celepar\Light\Neocep\Exceptions;

use Exception;

class NeocepException extends Exception
{
    /**
     * Cria a exceção
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    // personaliza a apresentação do objeto como string
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}