<?php

namespace Finite\Event\Callback;

use Finite\Event\TransitionEvent;

/**
 * Base interface for callbacks
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
interface CallbackInterface
{
    /**
     * @param object          $object
     * @param TransitionEvent $event
     */
    public function call($object, TransitionEvent $event);

    /**
     * @param TransitionEvent $event
     */
    public function __invoke(TransitionEvent $event);
}
