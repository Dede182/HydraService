<?php

namespace HydraService\Exceptions;

class HydraFileNotFound extends \Exception
{
    public function __construct($message = "Hydra File not found", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
