<?php

declare(strict_types=1);

namespace DREID\LaravelSepaXml\Services;

use DREID\LaravelSepaXml\Exceptions\MissingExtensionException;

class ExtensionChecker
{
    /**
     * @throws MissingExtensionException
     */
    public function checkExtension(string $extension): void
    {
        if (!extension_loaded($extension)) {
            throw new MissingExtensionException($extension);
        }
    }
}
