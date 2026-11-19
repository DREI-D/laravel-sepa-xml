<?php

declare(strict_types=1);

namespace DREID\LaravelSepaXml\Exceptions;

class LimitExceededException extends SepaException
{
    public static function numberOfTransactions(): self
    {
        return new self('The number of transactions exceeds the maximum per file.');
    }

    public static function valueOfTransactions(): self
    {
        return new self('The value of the transactions exceeds the maximum value per file.');
    }
}
