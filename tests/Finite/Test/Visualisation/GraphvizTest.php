<?php

namespace Finite\Test\Visualisation;

use Finite\Visualisation\Graphviz;
use Finite\Test\StateMachineTestCase;
use Finite\Visualisation\Exception;

/**
 * Tests the graphviz visualisation
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class GraphvizTest extends StateMachineTestCase
{
    /**
     * system under test
     * 
     * @var Finite\Visualisation\Graphviz
     */
    private $graphviz;

    protected function setUp()
    {
        parent::setUp();
        $this->initialize();
        
        $this->graphviz = new Graphviz();
    }
    
    public function testDotContainsTheNodes()
    {
        $target = sys_get_temp_dir() . '/test.dot';
        @unlink($target);
        $this->graphviz->render($this->object, $target);
        $this->assertFileExists($target);
        
        $content = file_get_contents($target);
        $this->assertContains('digraph state_machine {', $content);
        $this->assertContains('"s1" [shape=doublecircle]', $content, $content);
        $this->assertContains('"s5" [shape=circle]', $content, $content);
    }
    
    public function testDotContainsTheEdges()
    {
        $target = sys_get_temp_dir() . '/test.dot';
        @unlink($target);
        $this->graphviz->render($this->object, $target);
        $this->assertFileExists($target);
        
        $content = file_get_contents($target);
        $this->assertContains('"s1" -> "s2" [label="t12"]', $content, $content);
        $this->assertContains('"s4" -> "s5"', $content, $content);
    }

    public function testRendersToPng()
    {
        $target = sys_get_temp_dir() . '/test.png';
        @unlink($target);
        $this->graphviz->render($this->object, $target);
        $this->assertFileExists($target);
    }
    
    public function testFormatException()
    {
        $target = sys_get_temp_dir() . '/test.unkown';
        
        $this->setExpectedException('\Finite\Visualisation\Exception', Exception::CODE_DOT_ERROR);
        $this->graphviz->render($this->object, $target);
    }
}
