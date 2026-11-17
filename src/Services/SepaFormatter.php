<?php

declare(strict_types=1);

namespace DREID\LaravelSepaXml\Services;

use DOMDocument;
use DOMException;
use DREID\LaravelSepaXml\Exceptions\FormatException;
use DREID\LaravelSepaXml\Exceptions\MissingExtensionException;
use DREID\LaravelSepaXml\Interfaces\Formatter;

readonly class SepaFormatter implements Formatter
{
    public function __construct(private ExtensionChecker $extensionChecker) {}

    /**
     * @throws MissingExtensionException
     * @throws FormatException
     */
    public function minify(string $xml): string
    {
        $this->extensionChecker->checkExtension('dom');

        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($xml);

        try {
            $minifiedXml = $dom->saveXML();
        } catch (DOMException $exception) {
            throw FormatException::couldNotMinifyXml($exception);
        }

        if ($minifiedXml === false) {
            throw FormatException::couldNotMinifyXml();
        }

        return $minifiedXml;
    }

    /**
     * @throws MissingExtensionException
     * @throws FormatException
     */
    public function format(string $xml): string
    {
        $this->extensionChecker->checkExtension('dom');

        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml);

        try {
            $formattedXml = $dom->saveXML();
        } catch (DOMException $exception) {
            throw FormatException::couldNotFormatXml($exception);
        }

        if ($formattedXml === false) {
            throw FormatException::couldNotFormatXml();
        }

        return $formattedXml;
    }
}
