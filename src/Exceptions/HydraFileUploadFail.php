<?php

namespace HydraService\Exceptions;

class HydraFileUploadFail extends \Exception
{
    public function __construct($message = "Fail to store in provided storage", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
