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
     * @var \Finite\Visualisation\Graphviz
     */
    private $graphviz;

    protected function setUp()
    {
        parent::setUp();
        $this->initialize();
        $this->graphviz = new Graphviz(new \Finite\Visualisation\Configuration());
    }

    public function testRunsWithoutConfiguration()
    {
        $this->setExpectedException(null);
        new GraphViz();
    }
    
    public function testDotContainsTheNodes()
    {
        $content = $this->graphviz->render($this->object);
        $this->assertContains('digraph state_machine {', $content);
        $this->assertContains('"s1" [shape=doublecircle', $content, $content);
        $this->assertContains('"s5" [shape=circle', $content, $content);
    }

    public function testDotContainsTheEdges()
    {
        $content = $this->graphviz->render($this->object);
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

        $config = new \Finite\Visualisation\Configuration(true);
        $this->graphviz = new Graphviz($config);
        $content = $this->graphviz->render($this->object);
        
        $this->assertContains('property1', $content, $content);
        $this->assertContains('property2', $content, $content);
    }

    public function testMarksCurrentState()
    {
        $config = new \Finite\Visualisation\Configuration(false, 'red');
        $this->graphviz = new Graphviz($config);
        $this->graphviz->render($this->object);

        $content = $this->graphviz->render($this->object);
        $this->assertContains('label="s2", fillcolor=red', $content, $content);
        $this->assertNotContains('label="s3", fillcolor="red"', $content, $content);
    }
}

