Transitions properties
======================


As the second argument, `StateMachine#apply` and `StateMachine#test` will accept an array of properties to be passed
to the dispatched event, and accessible by the listeners.

Default properties can be defined with your state graph.

.. code-block:: php

    $stateManager->apply('some_event', array('something' => $value));


In your listeners you just have to call ```$event->getProperties()``` to access the passed data.

.. code-block:: php

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
            $params = $event->getProperties();

            $entity->setSomething($params['something']);
        }
    }


Default properties
------------------

.. code-block:: php

    'transitions' => array(
        'finish'    => array(
            'from' => array('middle'),
            'to'   => 'end',
            'properties' => array('foo' => 'bar'),
            'configure_properties' => function (OptionsResolver $resolver) {
                $resolver->setRequired('baz');
            }
        )
    )

