<?php

namespace Finite\Test\Visualisation;

use Finite\Visualisation\Graphviz;

use Finite\Event\StateMachineEvent;
use Finite\Event\TransitionEvent;
use Finite\StateMachine\ListenableStateMachine;
use Finite\Test\StateMachineTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
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

}
