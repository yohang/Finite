<?php

declare(strict_types=1);

namespace Finite;

use Finite\Event\CanTransitionEvent;
use Finite\Event\EventDispatcher;
use Finite\Event\PostTransitionEvent;
use Finite\Event\PreTransitionEvent;
use Finite\Exception\TransitionNotReachableException;
use Finite\Extractor\MemoizedStatePropertyExtractor;
use Finite\Extractor\StatePropertyExtractor;
use Finite\Transition\TransitionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @api
 */
class StateMachine
{
    public function __construct(
        private readonly EventDispatcherInterface $dispatcher = new EventDispatcher(),
        private readonly StatePropertyExtractor $statePropertyExtractor = new MemoizedStatePropertyExtractor(),
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

        $property = $this->statePropertyExtractor->extract($object, $stateClass);
        $fromState = $this->extractState($object, $stateClass);
        $transition = array_values(
            array_filter(
                $this->extractState($object, $stateClass)::getTransitions(),
                fn (TransitionInterface $transition) => $transition->getName() === $transitionName,
            )
        )[0];

        $this->dispatcher->dispatch(new PreTransitionEvent($object, $transition, $fromState));

        $transition->process($object);
        $property->setValue($object, $transition->getTargetState());

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
        return array_map(
            fn (\ReflectionProperty $property): string => (string) $property->getType(),
            $this->statePropertyExtractor->extractAll($object),
        );
    }

    public function hasState(object $object): bool
    {
        return \count($this->statePropertyExtractor->extractAll($object)) > 0;
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
        $property = $this->statePropertyExtractor->extract($object, $stateClass);

        /** @var State&\BackedEnum $value */
        $value = $property->getValue($object);

        return $value;
    }
}
