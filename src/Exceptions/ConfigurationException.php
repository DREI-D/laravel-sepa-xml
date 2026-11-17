<?php

declare(strict_types=1);

namespace DREID\LaravelSepaXml\Exceptions;

class ConfigurationException extends SepaException
{
    public function __construct()
    {
        parent::__construct('The configuration seems to be incomplete.');
    }
}
