<?php

declare(strict_types=1);

namespace DaggerModule;

use Dagger\Attribute\DaggerFunction;
use Dagger\Attribute\DaggerObject;
use Dagger\Attribute\Doc;
use Dagger\Container;
use Dagger\Directory;

use function Dagger\dag;

#[DaggerObject]
#[Doc('Finite dagger functions')]
final class Finite
{
    private const PHP_VERSIONS = ['8.1', '8.2', '8.3', '8.4'];

    #[DaggerFunction]
    #[Doc('Build test environnment')]
    public function build(Directory $source, string $phpVersion = '8.4', bool $preferLowest = false): Container
    {
        $composerCache = dag()->cacheVolume('composer');

        $composer = dag()
            ->container()
            ->from('composer:2');

        $installPhpExtensions = dag()
            ->http('https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions');

        $container = dag()
            ->container()
            ->from('php:' . $phpVersion)
            ->withFile('/usr/local/bin/install-php-extensions', $installPhpExtensions, 0755)
            ->withExec(['install-php-extensions', 'zip', 'opcache', 'pcov'])
            ->withFile('/usr/bin/composer', $composer->file('/usr/bin/composer'))
            ->withWorkdir('/app')
            ->withFile('composer.json', $source->file('composer.json'))
            ->withMountedCache('/root/.composer/cache', $composerCache);

        if ($preferLowest) {
            $container = $container
                ->withExec(['composer', 'update', '--prefer-lowest']);
        }

        return $container
            ->withExec(['composer', 'install'])
            ->withDirectory('examples', $source->directory('examples'))
            ->withDirectory('src', $source->directory('src'))
            ->withDirectory('tests', $source->directory('tests'))
            ->withFile('.php-cs-fixer.dist.php', $source->file('.php-cs-fixer.dist.php'))
            ->withFile('phpunit.xml.dist', $source->file('phpunit.xml.dist'))
            ->withFile('psalm.xml', $source->file('psalm.xml'));
    }

    #[DaggerFunction]
    #[Doc('Run test suite')]
    public function test(Directory $source, string $phpVersion = '8.4', bool $preferLowest = false): string
    {
        return $this
            ->build($source, $phpVersion, $preferLowest)
            ->withExec(['php', './vendor/bin/phpunit', '--coverage-text'])
            ->stdout();
    }

    #[DaggerFunction]
    #[Doc('Run test suite for all supported PHP versions')]
    public function testAll(Directory $source): string
    {
        $output = '';
        foreach (self::PHP_VERSIONS as $phpVersion) {
            $output .= $this->test($source, $phpVersion);
            $output .= $this->test($source, $phpVersion, true);
        }

        return $output;
    }

    #[DaggerFunction]
    #[Doc('Psalm static analysis')]
    public function psalm(Directory $source): string
    {
        return $this
            ->build($source, '8.1')
            ->withExec(['./vendor/bin/psalm', '--show-info=true', '--no-diff'])
            ->stdout();
    }

    #[DaggerFunction]
    #[Doc('PHP-CS-Fixer static analysis')]
    public function phpCsFixer(Directory $source): string
    {
        return $this
            ->build($source, '8.3')
            ->withExec(['./vendor/bin/php-cs-fixer', 'fix', '--dry-run', '--diff', '--ansi'])
            ->stdout();
    }
}
