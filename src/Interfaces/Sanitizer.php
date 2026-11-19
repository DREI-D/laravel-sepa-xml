<?php

declare(strict_types=1);

namespace DREID\LaravelSepaXml\Interfaces;

use DREID\LaravelSepaXml\Exceptions\MissingExtensionException;

interface Sanitizer
{
    /**
     * @throws MissingExtensionException
     */
    public function sanitize(string $value): string;

    public function sanitizeBankDetails(string $value): string;
}
