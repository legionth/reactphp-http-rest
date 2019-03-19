<?php

namespace Tests\Legionth\React\Http\Rest;


use Legionth\React\Http\Rest\Paramaters\Label\Colon;

class ColonStrategyTest extends TestCase
{
    public function testColonWillBeExtracted()
    {
        $colonStrategy = new Colon();
        $pathArray = array('say', ':word');
        $requestArray = array('say', 'hello');

        $result = $colonStrategy->extractParametersFromPath($pathArray, $requestArray);

        $this->assertEquals(array('word' => 'hello'), $result);
    }

    public function testNothingWillBeExtracted()
    {
        $colonStrategy = new Colon();
        $pathArray = array('say', 'word');
        $requestArray = array('say', 'hello');

        $result = $colonStrategy->extractParametersFromPath($pathArray, $requestArray);

        $this->assertEquals(array(), $result);
    }
}
