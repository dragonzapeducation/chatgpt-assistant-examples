<?php


namespace Dragonzap\OpenAI\ChatGPT\Exceptions;
use Exception;
class UnsupportedRunException extends Exception {

    public function __construct($message = "", $code = 0, $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

