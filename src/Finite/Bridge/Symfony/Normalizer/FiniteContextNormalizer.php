<?php

namespace Finite\Bridge\Symfony\Normalizer;

use Finite\Exception\Exception;
use Finite\Exception\FactoryException;
use Finite\Factory\FactoryInterface;
use Finite\StateMachine\StateMachineInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

class FiniteContextNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface
{
    private ?NormalizerInterface $decorated;

    private FactoryInterface $finite;

    public function __construct(
        FactoryInterface    $finite,
        NormalizerInterface $decorated = null
    )
    {
        $this->finite    = $finite;
        $this->decorated = $decorated;
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        if ($this->decorated && $this->decorated->supportsNormalization($data, $format)) {
            return true;
        }

        if (!is_object($data)) {
            return false;
        }

        try {
            $this->finite->get($data);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function normalize($object, $format = null, array $context = []): array
    {
        $data = $this->decorated ? $this->decorated->normalize($object, $format, $context) : [];

        if (!is_object($object)) {
            return $data;
        }

        try {
            $stateMachines = $this->finite->getAllForObject($object);
            if (!$stateMachines) {
                return $data;
            }

            $data['_finite'] = [
                ...array_map(
                    fn (StateMachineInterface $stateMachine) => [
                        'graphName'    => $stateMachine->getGraph(),
                        'currentState' => [
                            'name'       => $stateMachine->getCurrentState()->getName(),
                            'properties' => $stateMachine->getCurrentState()->getProperties(),
                        ],
                        'availableTransitions' => array_values(array_filter(
                            $stateMachine->getTransitions(),
                            static fn (string $transitionName) => $stateMachine->can($transitionName),
                        )),
                    ],
                    iterator_to_array($stateMachines),
                ),
            ];

            return $data;
        } catch (FactoryException $e) {
            return $data;
        }
    }

    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return $this->decorated && $this->decorated->supportsDenormalization($data, $type, $format);
    }

    public function denormalize($data, string $class, string $format = null, array $context = [])
    {
        return $this->decorated->denormalize($data, $class, $format, $context);
    }

    public function setSerializer(SerializerInterface $serializer): void
    {
        if ($this->decorated instanceof SerializerAwareInterface) {
            $this->decorated->setSerializer($serializer);
        }
    }
}
