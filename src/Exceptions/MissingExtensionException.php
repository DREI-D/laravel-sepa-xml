<?php

declare(strict_types=1);

namespace DREID\LaravelSepaXml\Exceptions;

class MissingExtensionException extends SepaException
{
    public function __construct(public readonly string $extension)
    {
        parent::__construct('The following extension seems to be missing: ' . $extension);
    }
}
