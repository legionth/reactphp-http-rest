<?php

namespace Legionth\React\Http\Rest\Paramaters\Label;

interface Strategy
{
    public function extractParametersFromPath(array $pathAsArray, array $requestPathArray) : array;

    public function getFirstIdentifier() : string;
}
