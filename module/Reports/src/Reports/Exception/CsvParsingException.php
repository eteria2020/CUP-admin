<?php

namespace Reports\Exception;

class CsvParsingException extends \Exception
{
    protected $message = 'Error trying to parse CSV data.';
}
