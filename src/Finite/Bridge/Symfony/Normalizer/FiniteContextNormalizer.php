<?php

namespace Finite\Bridge\Symfony\Normalizer;

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

        try {
            $this->finite->get($data);

            return true;
        } catch (FactoryException $e) {
            return false;
        }
    }

    public function normalize($object, $format = null, array $context = []): array
    {
        $data = $this->decorated ? $this->decorated->normalize($object, $format, $context) : [];

        try {
            $stateMachines = $this->finite->getAllForObject($data);
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
                    ],
                    iterator_to_array($stateMachines),
                ),
            ];
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