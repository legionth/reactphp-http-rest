<?php

namespace Tests\Legionth\React\Http\Rest;


use Legionth\React\Http\Rest\Paramaters\Label\CurlyBrackets;

class CurlyBracketsStrategyTest extends TestCase
{
    public function testValueWillBeExtracted()
    {
        $colonStrategy = new CurlyBrackets();
        $pathArray = array('say', '{word}');
        $requestArray = array('say', 'hello');

        $result = $colonStrategy->extractParametersFromPath($pathArray, $requestArray);

        $this->assertEquals(array('word' => 'hello'), $result);
    }

    public function testNothingWillBeExtracted()
    {
        $colonStrategy = new CurlyBrackets();
        $pathArray = array('say', ':word');
        $requestArray = array('say', 'hello');

        $result = $colonStrategy->extractParametersFromPath($pathArray, $requestArray);

        $this->assertEquals(array(), $result);
    }

    public function testNothingWillBeExtractedBecauseBracketIsNotComplete()
    {
        $colonStrategy = new CurlyBrackets();
        $pathArray = array('say', '{word');
        $requestArray = array('say', 'hello');

        $result = $colonStrategy->extractParametersFromPath($pathArray, $requestArray);

        $this->assertEquals(array(), $result);
    }
}
