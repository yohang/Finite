### Work with states and transitions

{% highlight php %}
<?php
echo $stateMachine->getCurrentState();
// => "draft"

var_dump($stateMachine->can('accept'));
// => bool(false)

var_dump($stateMachine->can('propose'));
// => bool(true)

$stateMachine->apply('propose');
echo $stateMachine->getCurrentState();
// => "proposed"

{% endhighlight %}
