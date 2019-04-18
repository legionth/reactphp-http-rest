<?php

namespace Legionth\React\Http\Rest\Paramaters\Label;

class ExtractBetweenTwoCharacters
{
    public function extract(string $value, string $firstCharacter, string $secondCharacter) : string
    {
        $start = strpos($value, $firstCharacter);
        if (false === $start || 0 !== $start) {
            return '';
        }

        $end    = strpos($value, $secondCharacter, $start + 1);
        if (false === $end || $end < $start) {
            return '';
        }

        $length = $end - $start;
        $extractedName = substr($value, $start + 1, $length - 1);

        return $extractedName;
    }
}
