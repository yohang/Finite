### Define a workflow / State graph

{% highlight php %}
<?php

$document     = new MyDocument;
$stateMachine = new StateMachine;
$loader       = new Finite\Loader\ArrayLoader(
    array(
        'class'  => 'MyDocument',
        'states' => array(
            'draft' => array(
                'type'       => 'initial',
                'properties' => array()
            ),
            'proposed' => array(
                'type'       => 'normal',
                'properties' => array()
            ),
            'accepted' => array(
                'type'       => 'final',
                'properties' => array()
            ),
            'refused' => array(
                'type'       => 'final',
                'properties' => array()
            ),
        ),
        'transitions' => array(
            'propose' => array(
                'from' => array('draft'),
                'to'   => 'proposed',
            ),
            'accept' => array(
                'from' => array('proposed'),
                'to'   => 'accepted',
            ),
            'refuse' => array(
                'from' => array('proposed'),
                'to'   => 'refused',
            ),
        )
    )
);

$loader->load($stateMachine);
$stateMachine->setObject($document);
$stateMachine->initialize();

{% endhighlight %}
