### Define a workflow / State graph

{% highlight php %}
<?php

$document     = new MyDocument;
$stateMachine = new StateMachine;
$loader       = new Finite\Loader\ArrayLoader([
    'class'  => 'MyDocument',
    'states' => [
        'draft'    => ['type' => 'initial', 'properties' => []],
        'proposed' => ['type' => 'normal',  'properties' => []],
        'accepted' => ['type' => 'final',   'properties' => []],
        'refused'  => ['type' => 'final',   'properties' => []],
    ],
    'transitions' => [
        'propose' => ['from' => ['draft'],    'to' => 'proposed'],
        'accept'  => ['from' => ['proposed'], 'to' => 'accepted'],
        'refuse'  => ['from' => ['proposed'], 'to' => 'refused'],
    ]
]);

$loader->load($stateMachine);
$stateMachine->setObject($document);
$stateMachine->initialize();

{% endhighlight %}
