<?php

namespace Finite\Event\Callback;

use Finite\Event\TransitionEvent;

/**
 * Base interface for callbacks.
 *
 * @author Yohan Giarelli <yohan@giarel.li>
 */
interface CallbackInterface
{
    /**
     * @param TransitionEvent $event
     */
    public function __invoke(TransitionEvent $event);
}
