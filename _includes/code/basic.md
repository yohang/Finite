### Work with states and transitions

{% highlight php %}
<?php
echo $stateMachine->getState();
// => "draft"

var_dump($stateMachine->can('accept'));
// => bool(false)

var_dump($stateMachine->can('propose'));
// => bool(true)

$stateMachine->apply('propose');
echo $stateMachine->getState();
// => "proposed"

{% endhighlight %}
