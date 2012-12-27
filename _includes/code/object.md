### Define your object

{% highlight php %}
<?php
class MyDocument implements Finite\StatefulInterface
{
    private $state;
    public function getFiniteState()
    {
        return $this->state;
    }
    public function setFiniteState($state)
    {
        $this->state = $state;
    }
}

{% endhighlight %}
