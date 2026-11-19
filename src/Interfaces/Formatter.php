<?php

declare(strict_types=1);

namespace DREID\LaravelSepaXml\Interfaces;

use DREID\LaravelSepaXml\Exceptions\FormatException;
use DREID\LaravelSepaXml\Exceptions\MissingExtensionException;

interface Formatter
{
    /**
     * @throws MissingExtensionException
     * @throws FormatException
     */
    public function format(string $xml): string;

    /**
     * @throws MissingExtensionException
     * @throws FormatException
     */
    public function minify(string $xml): string;
}
