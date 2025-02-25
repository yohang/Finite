<?php

declare(strict_types=1);

namespace Finite;

use Finite\Event\CanTransitionEvent;
use Finite\Event\EventDispatcher;
use Finite\Event\PostTransitionEvent;
use Finite\Event\PreTransitionEvent;
use Finite\Exception\BadStateClassException;
use Finite\Exception\NonUniqueStateException;
use Finite\Exception\NoStateFoundException;
use Finite\Exception\TransitionNotReachableException;
use Finite\Transition\TransitionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @api
 */
class StateMachine
{
    public function __construct(
        private readonly EventDispatcherInterface $dispatcher = new EventDispatcher(),
    ) {
    }

    /**
     * @param class-string|null $stateClass
     */
    public function apply(object $object, string $transitionName, ?string $stateClass = null): void
    {
        if (!$this->can($object, $transitionName, $stateClass)) {
            throw new TransitionNotReachableException('Unable to apply transition '.$transitionName);
        }

        $property = $this->extractStateProperty($object, $stateClass);
        $fromState = $this->extractState($object, $stateClass);
        $transition = array_values(
            array_filter(
                $this->extractState($object, $stateClass)::getTransitions(),
                fn (TransitionInterface $transition) => $transition->getName() === $transitionName,
            )
        )[0];

        $this->dispatcher->dispatch(new PreTransitionEvent($object, $transition, $fromState));

        $transition->process($object);
        PropertyAccess::createPropertyAccessor()->setValue(
            $object,
            $property->getName(),
            $transition->getTargetState(),
        );

        /** @var object $object */
        $this->dispatcher->dispatch(new PostTransitionEvent($object, $transition, $fromState));
    }

    /**
     * @param class-string|null $stateClass
     */
    public function can(object $object, string $transitionName, ?string $stateClass = null): bool
    {
        $fromState = $this->extractState($object, $stateClass);
        foreach ($fromState::getTransitions() as $transition) {
            if ($transition->getName() !== $transitionName) {
                continue;
            }

            if (!\in_array($fromState, $transition->getSourceStates(), true)) {
                return false;
            }

            $event = new CanTransitionEvent($object, $transition, $fromState);
            $this->dispatcher->dispatch($event);

            return !$event->isTransitionBlocked();
        }

        throw new TransitionNotReachableException(\sprintf('No transition "%s" found', $transitionName));
    }

    /**
     * @param class-string|null $stateClass
     */
    public function getReachablesTransitions(object $object, ?string $stateClass = null): array
    {
        $state = $this->extractState($object, $stateClass);

        return array_filter(
            $state->getTransitions(),
            fn (TransitionInterface $transition) => $this->can($object, $transition->getName(), $stateClass),
        );
    }

    /**
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     *
     * @return array<int, enum-string<State>>
     */
    public function getStateClasses(object $object): array
    {
        return array_filter(
            array_map(
                fn (\ReflectionProperty $property): string => (string) $property->getType(),
                $this->extractStateProperties($object),
            ),
            fn (?string $name): bool => enum_exists($name),
        );
    }

    public function hasState(object $object): bool
    {
        return \count($this->extractStateProperties($object)) > 0;
    }

    public function getDispatcher(): EventDispatcherInterface
    {
        return $this->dispatcher;
    }

    /**
     * @param class-string|null $stateClass
     */
    private function extractState(object $object, ?string $stateClass = null): State&\BackedEnum
    {
        $property = $this->extractStateProperty($object, $stateClass);

        /** @psalm-suppress MixedReturnStatement */
        return PropertyAccess::createPropertyAccessor()->getValue($object, $property->getName());
    }

    /**
     * @param class-string|null $stateClass
     */
    private function extractStateProperty(object $object, ?string $stateClass = null): \ReflectionProperty
    {
        if ($stateClass && !enum_exists($stateClass)) {
            throw new NoStateFoundException(\sprintf('Enum "%s" does not exists', $stateClass));
        }

        $properties = $this->extractStateProperties($object);
        if (null !== $stateClass) {
            foreach ($properties as $property) {
                if ((string) $property->getType() === $stateClass) {
                    return $property;
                }
            }

            throw new BadStateClassException(\sprintf('Found no state on object "%s" with class "%s"', $object::class, $stateClass));
        }

        if (1 === \count($properties)) {
            return $properties[0];
        }

        if (\count($properties) > 1) {
            throw new NonUniqueStateException('Found multiple states on object '.$object::class);
        }

        throw new NoStateFoundException('Found no state on object '.$object::class);
    }

    /**
     * @return array<int, \ReflectionProperty>
     */
    private function extractStateProperties(object $object): array
    {
        $properties = [];

        $reflectionClass = new \ReflectionClass($object);
        /** @psalm-suppress DocblockTypeContradiction */
        do {
            foreach ($reflectionClass->getProperties() as $property) {
                /** @var \ReflectionUnionType|\ReflectionIntersectionType|\ReflectionNamedType $reflectionType */
                $reflectionType = $property->getType();
                if (null === $reflectionType) {
                    continue;
                }

                if ($reflectionType instanceof \ReflectionUnionType) {
                    continue;
                }

                if ($reflectionType instanceof \ReflectionIntersectionType) {
                    continue;
                }

                /** @var class-string $name */
                $name = $reflectionType->getName();
                if (!enum_exists($name)) {
                    continue;
                }

                $reflectionEnum = new \ReflectionEnum($name);
                /** @psalm-suppress TypeDoesNotContainType */
                if ($reflectionEnum->implementsInterface(State::class)) {
                    $properties[] = $property;
                }
            }
        } while ($reflectionClass = $reflectionClass->getParentClass());

        return $properties;
    }
}
