<?php

declare(strict_types=1);

namespace DREID\LaravelSepaXml\Exceptions;

use Throwable;

class RenderException extends SepaException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct('The sepa content could not be rendered.', previous: $previous);
    }
}
