<?php

namespace DREID\LaravelSepaXml\Services;

use Illuminate\Support\Str;

class SepaStringSanitizeService
{
    public function sanitize(string $subject): string
    {
        $subject = Str::upper($subject);

        $replaceRules = [
            'Ä' => 'AE',
            'Ü' => 'UE',
            'Ö' => 'OE',
            '/' => '-'
        ];

        foreach ($replaceRules as $replace => $with) {
            $subject = Str::replace($replace, $with, $subject);
        }

        $subject = iconv('utf-8', 'ascii//TRANSLIT', $subject);
        $subject = Str::upper($subject);

        return preg_replace(
            '/[^a-zA-Z0-9\-\. ]/',
            '',
            $subject
        );
    }
}
