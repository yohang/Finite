# Additional parameters for events

As the second argument, StateManager#apply will accept an array of parameters to be passed to the dispatched event, and accessible by the listeners.

```php
$stateManager->apply('some_event', ['something' => $value]);
```

In your listeners you just have to call ```$event->getParameters``` to access the passed data.

```php
<?php

namespace My\AwesomeBundle\EventListener;

use Finite\Event\TransitionEvent;

class TransitionListener
{
    /**
     * @param TransitionEvent $event
     */
    public function someEvent(TransitionEvent $event)
    {
        $entity = $event->getStateMachine()->getObject();
        $params = $event->getParameters();

        $entity->setSomething($params['something']);
    }
}
```

**TIP:** Use the OptionResolver symfony component to specify required and optional parameters.

```php
<?php

namespace My\AwesomeBundle\EventListener;

use Finite\Event\TransitionEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransitionListener
{
    /**
     * @param TransitionEvent $event
     */
    public function someEvent(TransitionEvent $event)
    {
        $entity = $event->getStateMachine()->getObject();

        $resolver = new OptionsResolver();
        $resolver->setRequired(['something']);

        $params = $resolver->resolve($event->getParameters());

        $entity->setSomething($params['something']);
    }
}
```
