<?php

declare(strict_types=1);

namespace DREID\LaravelSepaXml\Services;

use DREID\LaravelSepaXml\Exceptions\MissingExtensionException;
use DREID\LaravelSepaXml\Interfaces\Sanitizer;
use Illuminate\Support\Str;
use RuntimeException;

readonly class SepaSanitizer implements Sanitizer
{
    public function __construct(private ExtensionChecker $extensionChecker) {}

    /**
     * @throws MissingExtensionException
     */
    public function sanitize(string $value): string
    {
        $this->extensionChecker->checkExtension('iconv');

        $value = Str::upper($value);

        $replaceRules = [
            'Ä' => 'AE',
            'Ü' => 'UE',
            'Ö' => 'OE',
            '/' => '-',
        ];

        foreach ($replaceRules as $replace => $with) {
            $value = Str::replace($replace, $with, $value);
        }

        /** @noinspection PhpComposerExtensionStubsInspection */
        $value = iconv('utf-8', 'ascii//TRANSLIT', $value);

        if ($value === false) {
            throw new RuntimeException('Failed to convert value to ASCII.');
        }

        $value = Str::upper($value);

        $value = preg_replace(
            '/[^a-zA-Z0-9\-. ]/',
            '',
            $value
        );

        if (!is_string($value)) {
            throw new RuntimeException('preg_replace did not return a string as expected.');
        }

        return $value;
    }

    public function sanitizeBankDetails(string $value): string
    {
        $value = preg_replace(
            '/[^A-Z0-9]/',
            '',
            Str::upper($value)
        );

        if (!is_string($value)) {
            throw new RuntimeException('preg_replace did not return a string as expected.');
        }

        return $value;
    }
}
