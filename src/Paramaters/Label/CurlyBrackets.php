<?php

namespace Legionth\React\Http\Rest\Paramaters\Label;

class CurlyBrackets implements Strategy
{

    const PREFIX = '{';

    const SUFFIX = '}';

    /**
     * @var ExtractBetweenTwoCharacters
     */
    private $extractor;

    /**
     * @param ExtractBetweenTwoCharacters|null $extractor
     */
    public function __construct(ExtractBetweenTwoCharacters $extractor = null)
    {
        if (null === $extractor) {
            $extractor = new ExtractBetweenTwoCharacters();
        }
        $this->extractor = $extractor;
    }

    /**
     * @param array $pathAsArray
     * @param array $requestPathArray
     * @return array
     */
    public function extractParametersFromPath(array $pathAsArray, array $requestPathArray) : array
    {
        $result = array();
        foreach ($pathAsArray as $id => $valueName) {
            $extractedName = $this->extractor->extract($valueName, self::PREFIX, self::SUFFIX);

            if ('' !== $extractedName) {
                $result[$extractedName] = $requestPathArray[$id];
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getFirstIdentifier() : string
    {
        return self::PREFIX;
    }
}
