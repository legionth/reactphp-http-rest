<?php
namespace Legionth\React\Http\Rest\Paramaters\Label;

class Colon implements Strategy
{
    const IDENTIFIER = ':';

    public function extractParametersFromPath(array $pathAsArray, array $requestPathArray) : array
    {
        $result = array();
        foreach ($pathAsArray as $id => $valueName) {
            $position = strpos($valueName, self::IDENTIFIER);
            if (0 === $position) {
                $valueName = substr($valueName, 1);
                $result[$valueName] = $requestPathArray[$id];
            }
        }

        return $result;
    }

    public function getFirstIdentifier() : string
    {
        return self::IDENTIFIER;
    }
}
