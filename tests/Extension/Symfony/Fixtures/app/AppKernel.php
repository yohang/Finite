<?php

declare(strict_types=1);

use Finite\Tests\Extension\Symfony\Fixtures\Controller\FiniteController;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class AppKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        return [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Finite\Extension\Symfony\Bundle\FiniteBundle(),
        ];
    }

    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader): void
    {
        $c->setParameter('kernel.debug', true);
        $c->setParameter('kernel.secret', uniqid());
        $c->prependExtensionConfig('framework', ['test' => true, 'profiler' => true]);
        $c->prependExtensionConfig('twig', ['paths' => [__DIR__.'/templates']]);

        $c->addDefinitions(
            [
                FiniteController::class => (new Definition(FiniteController::class))
                    ->setAutowired(true)
                    ->addTag('controller.service_arguments'),
                'logger' => (new Definition(NullLogger::class)),
            ]
        );
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    public function loadRoutes(LoaderInterface $loader): RouteCollection
    {
        $collection = new RouteCollection();
        $collection->add(
            'finite',
            new Route('/finite', ['_controller' => FiniteController::class]),
        );

        return $collection;
    }
}
