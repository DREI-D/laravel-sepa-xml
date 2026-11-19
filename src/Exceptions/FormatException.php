<?php

declare(strict_types=1);

namespace DREID\LaravelSepaXml\Exceptions;

use Throwable;

class FormatException extends SepaException
{
    public static function couldNotFormatXml(?Throwable $previous = null): self
    {
        return new self('The sepa content could not be formatted.', previous: $previous);
    }

    public static function couldNotMinifyXml(?Throwable $previous = null): self
    {
        return new self('The sepa content could not be minified.', previous: $previous);
    }
}
