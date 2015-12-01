<?php

namespace Finite\Event;

/**
 * The class that contains event names.
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
final class FiniteEvents
{
    const
        /*
         * This event is thrown when an object got its initial state
         */
        SET_INITIAL_STATE = 'finite.set_initial_state',

        /*
         * This event is thrown each time a StateMachine is initialized
         */
        INITIALIZE = 'finite.initialize',

        /*
         * This event is thrown before transitions are processed
         */
        PRE_TRANSITION = 'finite.pre_transition',

        /*
         * This event is thrown after transitions are processed
         */
        POST_TRANSITION = 'finite.post_transition',

        /*
         * Fired when a transition test is made.
         */
        TEST_TRANSITION = 'finite.test_transition'
    ;
}
