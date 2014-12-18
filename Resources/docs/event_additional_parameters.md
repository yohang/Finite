# Additional parameters for events

As the second argument, StateManager#apply will accept an array of parameters to be passed to the dispatched event, and accessible by the listeners.

```php
$stateManager->apply('some_event', array('something' => $value));
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

**TIP:** Use the OptionsResolver Symfony component to handle required and optional parameters by setting it in your transition settings.

```php
$resolver = new OptionsResolver(); // ...

'transitions' => array(
    'finish'    => array(
        'from' => array('middle'),
        'to'   => 'end',
        'event_options_resolver' => $resolver,
    )
)
```
