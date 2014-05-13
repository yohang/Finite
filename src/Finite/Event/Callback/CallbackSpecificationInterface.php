<?php

namespace Finite\Event\Callback;

use Finite\Event\TransitionEvent;

/**
 * Base interface for CallbackSpecification
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
interface CallbackSpecificationInterface
{
    const ALL = 'all';

    /**
     * Return if this callback carried by this spec should be called on this event
     *
     * @param TransitionEvent $event
     *
     * @return boolean
     */
    public function supports(TransitionEvent $event);
}
