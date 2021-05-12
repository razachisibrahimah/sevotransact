<?php

namespace RazakIbrahimah\Sevotransact\Exceptions;

class CurlException extends \Exception
{
    public function __construct($message = '', $code = 0)
    {
        parent::__construct("cURL Error #:" . $message, $code);
    }
}
