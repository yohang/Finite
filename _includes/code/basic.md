### Basic usage

{% highlight php %}
<?php

// $document = Retrieve your stateful object

$stateMachine = $factory->get($document);

echo $stateMachine->getState()->getName();
// => string "state-1"

var_dump($stateMachine->can('an-available-transition'));
// => bool(true)

$stateMachine->apply('an-available-transition');
echo $stateMachine->getState()->getName();
// => string "state-2"

{% endhighlight %}
