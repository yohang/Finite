<?php

namespace Finite\Bundle\FiniteBundle;

use Finite\Bundle\FiniteBundle\DependencyInjection\Compiler\ContainerCallbackPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FiniteFiniteBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ContainerCallbackPass());
    }
}
