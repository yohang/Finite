<?php

namespace Finite\Event\Callback;

use Finite\Event\TransitionEvent;

/**
 * Base interface for CallbackSpecification.
 *
 * @author Yohan Giarelli <yohan@giarel.li>
 */
interface CallbackSpecificationInterface
{
    /**
     * Return if this callback carried by this spec should be called on this event.
     *
     * @param TransitionEvent $event
     *
     * @return bool
     */
    public function isSatisfiedBy(TransitionEvent $event);
}
