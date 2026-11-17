<?php

declare(strict_types=1);

namespace DREID\LaravelSepaXml\Services;

use DREID\LaravelSepaXml\Exceptions\MissingExtensionException;
use DREID\LaravelSepaXml\Interfaces\Sanitizer;
use Illuminate\Support\Str;

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

        $value = iconv('utf-8', 'ascii//TRANSLIT', $value);
        $value = Str::upper($value);

        return preg_replace(
            '/[^a-zA-Z0-9\-. ]/',
            '',
            $value
        );
    }

    public function sanitizeBankDetails(string $value): string
    {
        return preg_replace(
            '/[^A-Z0-9]/',
            '',
            Str::upper($value)
        );
    }
}
