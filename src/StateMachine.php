<?php

namespace Finite;

use Finite\Event\CanTransitionEvent;
use Finite\Event\EventDispatcher;
use Finite\Event\PostTransitionEvent;
use Finite\Event\PreTransitionEvent;
use Finite\Transition\TransitionInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class StateMachine
{
    public function __construct(
        private readonly EventDispatcher $dispatcher = new EventDispatcher,
    )
    {

    }

    public function apply(object $object, string $transitionName, ?string $stateClass = null): void
    {
        if (!$this->can($object, $transitionName, $stateClass)) {
            throw new \InvalidArgumentException('Unable to apply transition ' . $transitionName);
        }

        $property   = $this->extractStateProperty($object, $stateClass);
        $transition = array_values(
                          array_filter(
                              $this->extractState($object, $stateClass)::getTransitions(),
                              fn(TransitionInterface $transition) => $transition->getName() === $transitionName,
                          )
                      )[0];

        $this->dispatcher->dispatch(new PreTransitionEvent($object, $transitionName, $stateClass));

        $transition->process($object);
        PropertyAccess::createPropertyAccessor()->setValue(
            $object,
            $property->getName(),
            $transition->getTargetState(),
        );

        /** @var object $object */
        $this->dispatcher->dispatch(new PostTransitionEvent($object, $transitionName, $stateClass));
    }

    public function can(object $object, string $transitionName, ?string $stateClass = null): bool
    {
        $state = $this->extractState($object, $stateClass);
        foreach ($state::getTransitions() as $transition) {
            if ($transition->getName() !== $transitionName) {
                continue;
            }

            if (!in_array($state, $transition->getSourceStates())) {
                return false;
            }

            $event = new CanTransitionEvent($object, $transitionName, $stateClass);
            $this->dispatcher->dispatch($event);

            return !$event->isTransitionBlocked();
        }

        throw new \InvalidArgumentException(sprintf('No transition "%s" found', $transitionName));
    }

    public function getReachablesTransitions(object $object, ?string $stateClass = null): array
    {
        $state = $this->extractState($object, $stateClass);

        return array_filter(
            $state->getTransitions(),
            fn(TransitionInterface $transition) => $this->can($object, $transition->getName(), $stateClass),
        );
    }

    private function extractState(object $object, ?string $stateClass = null): State
    {
        $property = $this->extractStateProperty($object, $stateClass);

        return PropertyAccess::createPropertyAccessor()->getValue($object, $property->getName());
    }

    private function extractStateProperty(object $object, ?string $stateClass = null): \ReflectionProperty
    {
        if ($stateClass && !enum_exists($stateClass)) {
            throw new \InvalidArgumentException(sprintf('Enum "%s" does not exists', $stateClass));
        }

        $reflectionClass = new \ReflectionClass($object);
        do {
            foreach ($reflectionClass->getProperties() as $property) {
                if (!enum_exists($property->getType()->getName())) {
                    continue;
                }

                if ($property instanceof \ReflectionUnionType) {
                    continue;
                }

                $reflectionEnum = new \ReflectionEnum($property->getType()->getName());
                if (
                    null !== $stateClass &&
                    (
                        $reflectionEnum->getName() === $stateClass ||
                        (interface_exists($stateClass) && $reflectionEnum->implementsInterface($stateClass))
                    )
                ) {
                    return $property;
                }

                if (null === $stateClass && $reflectionEnum->implementsInterface(State::class)) {
                    return $property;
                }

            }
        } while (null !== ($reflectionClass = $reflectionClass->getParentClass()));

        throw new \InvalidArgumentException('Found no state on object ' . get_class($object));
    }
}
