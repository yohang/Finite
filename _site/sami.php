<?php

namespace CalendRDoc
{
    class TwigExtension extends \Twig_Extension
    {
        public function getFunctions()
        {
            return array(
                'contains' => new \Twig_Function_Method($this, 'contains'),
            );
        }

        public function getFilters()
        {
            return array(
                'remove_vendor'  => new \Twig_Filter_Method($this, 'removeVendor'),
                'namespace_sort' => new \Twig_Filter_Method($this, 'namespaceSort'),
                'markdownify'    => new \Twig_Filter_Method($this, 'markdownify', array('is_safe' => array('html'))),
            );
        }

        public function markdownify($value)
        {
            $captureString = '{% capture the_text_to_capture %}';
            $endCaptureString = '{% endcapture %}{{ the_text_to_capture | markdownify }}';

            $value = $captureString .$value. $endCaptureString;

            $value = preg_replace_callback(
                '#```([-_a-z0-9]+)\\n(.*)```#s',
                function($matches) use ($captureString, $endCaptureString) {
                    if ('php' == $matches[1] && false === strpos($matches[2], '<?php')) {
                        $matches[2] = "<?php\n".$matches[2];
                    }

                    return sprintf(
                        '%s{%% highlight %s %%}%s{%% endhighlight %%}%s',
                        $endCaptureString,
                        $matches[1],
                        $matches[2],
                        $captureString
                    );
                },
                $value
            );

            return $value;
        }

        public function removeVendor($value)
        {
            return substr($value, 7);
        }

        public function namespaceSort($value)
        {
            uasort($value, function(\Sami\Reflection\ClassReflection $a, \Sami\Reflection\ClassReflection $b) {
                if ($a->getNamespace() == $b->getNamespace()) {
                    return $a->getShortName() > $b->getShortName();
                }

                return $a->getNamespace() > $b->getNamespace();
            });

            return $value;
        }

        public function contains($haystack, $needle)
        {
            return false !== strpos($haystack, $needle);
        }

        public function getName()
        {
            return 'calendr_doc';
        }
    }
}

namespace
{
    use Sami\Sami;
    use Symfony\Component\Finder\Finder;

    $iterator = Finder::create()
        ->files()
        ->name('*.php')
        ->in(realpath(__DIR__.'/../src'))
    ;

    $sami = new Sami($iterator, array(
        'base_url'             => 'http://yohang.github.com/Finite',
        'template_dirs'        => array(__DIR__.'/_sami/themes'),
        'theme'                => 'calendr',
        'title'                => 'Finite API',
        'build_dir'            => __DIR__.'/api',
        'cache_dir'            => __DIR__.'/cache',
        'default_opened_level' => 2,
    ));

    $sami['twig']->addExtension(new \CalendRDoc\TwigExtension);


    return $sami;
}
