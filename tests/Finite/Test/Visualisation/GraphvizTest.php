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

    private $target;
    
    protected function setUp()
    {
        parent::setUp();
        $this->initialize();
        
        $this->target = sys_get_temp_dir() . '/test.dot';
        @unlink($this->target);
        $this->graphviz = new Graphviz(new \Finite\Visualisation\Configuration($this->target));
    }
    
    public function testDotContainsTheNodes()
    {
        
        $this->graphviz->render($this->object);
        $this->assertFileExists($this->target);
        
        $content = file_get_contents($this->target);
        $this->assertContains('digraph state_machine {', $content);
        $this->assertContains('"s1" [shape=doublecircle', $content, $content);
        $this->assertContains('"s5" [shape=circle', $content, $content);
    }
    
    public function testDotContainsTheEdges()
    {
        $this->graphviz->render($this->object);
        $this->assertFileExists($this->target);
        
        $content = file_get_contents($this->target);
        $this->assertContains('"s1" -> "s2" [label="t12"]', $content, $content);
        $this->assertContains('"s4" -> "s5"', $content, $content);
    }
    
    public function testRendersProperties()
    {
        $state = new \Finite\State\State(
            'YAS',
            \Finite\State\State::TYPE_FINAL,
            array(),
            array('property1' => true, 'property2' => false)
        );
        $this->object->addState($state);
        $this->object->addTransition('t4yas', 's4', 'YAS');
        
        $config = new \Finite\Visualisation\Configuration($this->target, true);
        $this->graphviz = new Graphviz($config);
        $this->graphviz->render($this->object);
        
        $content = file_get_contents($this->target);
        $this->assertContains('property1', $content, $content);
        $this->assertContains('property2', $content, $content);
    }
    
    public function testMarksCurrentState()
    {
        $config = new \Finite\Visualisation\Configuration($this->target, false, 'red');
        $this->graphviz = new Graphviz($config);
        $this->graphviz->render($this->object);
        
        $content = file_get_contents($this->target); echo $content;
        $this->assertContains('label="s2", fillcolor=red', $content, $content);
        $this->assertNotContains('label="s3", fillcolor="red"', $content, $content);
    }

    public function testRendersToPng()
    {
        $this->assertDotIsExecutable();
        $target = sys_get_temp_dir() . '/test.png';
        @unlink($target);
        $this->graphviz = new Graphviz(new \Finite\Visualisation\Configuration($target));
        $this->graphviz->render($this->object);
        $this->assertFileExists($target);
    }
    
    public function testFormatException()
    {
        $this->assertDotIsExecutable();
        $target = sys_get_temp_dir() . '/test.unkown';
        $this->graphviz = new Graphviz(new \Finite\Visualisation\Configuration($target));
        $this->setExpectedException('\Finite\Visualisation\Exception', Exception::CODE_DOT_ERROR);
        $this->graphviz->render($this->object);
    }
    
    private function assertDotIsExecutable()
    {
        $returnVal = shell_exec("which dot");
        if (empty($returnVal)) {
            $this->markTestSkipped('dot is not executable on this system.');
        }
    }
}
