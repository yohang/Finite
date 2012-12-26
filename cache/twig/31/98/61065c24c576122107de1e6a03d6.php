<?php

/* macros.twig */
class __TwigTemplate_319861065c24c576122107de1e6a03d6 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 4
        echo "
";
        // line 16
        echo "
";
        // line 22
        echo "
";
        // line 28
        echo "
";
        // line 42
        echo "
";
        // line 46
        echo "
";
        // line 58
        echo "
";
    }

    // line 1
    public function getattributes($attributes = null)
    {
        $context = $this->env->mergeGlobals(array(
            "attributes" => $attributes,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 2
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getContext($context, "attributes"));
            foreach ($context['_seq'] as $context["key"] => $context["value"]) {
                echo " ";
                echo twig_escape_filter($this->env, $this->getContext($context, "key"), "html", null, true);
                echo "=\"";
                echo twig_escape_filter($this->env, $this->getContext($context, "value"), "html", null, true);
                echo "\"";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['key'], $context['value'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
        } catch(Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ob_get_clean();
    }

    // line 5
    public function getclass_link($class = null, $attributes = null, $absolute = null)
    {
        $context = $this->env->mergeGlobals(array(
            "class" => $class,
            "attributes" => $attributes,
            "absolute" => $absolute,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 6
            if ($this->getAttribute($this->getContext($context, "class"), "projectclass")) {
                // line 7
                echo "<a href=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($this, "class_href", array(0 => $this->getContext($context, "class")), "method"), "html", null, true);
                echo "\"";
                echo twig_escape_filter($this->env, $this->getAttribute($this, "attributes", array(0 => $this->getContext($context, "attributes")), "method"), "html", null, true);
                echo "
           data-content=\"";
                // line 8
                echo nl2br(twig_escape_filter($this->env, $this->env->getExtension('sami')->parseDesc($context, $this->getAttribute($this->getContext($context, "class"), "shortdesc"), $this->getContext($context, "class")), "html", null, true));
                echo "\"
           data-original-title=\"";
                // line 9
                echo twig_escape_filter($this->env, $this->env->getExtension('calendr_doc')->removeVendor($this->getContext($context, "class")), "html", null, true);
                echo "\" rel=\"popover\">";
            } elseif ($this->getAttribute($this->getContext($context, "class"), "phpclass")) {
                // line 11
                echo "<a href=\"http://php.net/";
                echo twig_escape_filter($this->env, $this->getContext($context, "class"), "html", null, true);
                echo "\"";
                echo twig_escape_filter($this->env, $this->getAttribute($this, "attributes", array(0 => $this->getContext($context, "attributes")), "method"), "html", null, true);
                echo ">";
            }
            // line 13
            echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "class"), "shortname"), "html", null, true);
            // line 14
            if (($this->getAttribute($this->getContext($context, "class"), "projectclass") || $this->getAttribute($this->getContext($context, "class"), "phpclass"))) {
                echo "</a>";
            }
        } catch(Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ob_get_clean();
    }

    // line 17
    public function getmethod_link($method = null, $attributes = null, $absolute = null, $classonly = null)
    {
        $context = $this->env->mergeGlobals(array(
            "method" => $method,
            "attributes" => $attributes,
            "absolute" => $absolute,
            "classonly" => $classonly,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 18
            echo "<a href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('sami')->pathForMethod($context, $this->getContext($context, "method")), "html", null, true);
            echo "\"";
            echo twig_escape_filter($this->env, $this->getAttribute($this, "attributes", array(0 => $this->getContext($context, "attributes")), "method"), "html", null, true);
            echo ">";
            // line 19
            echo twig_escape_filter($this->env, $this->getAttribute($this, "abbr_class", array(0 => $this->getAttribute($this->getContext($context, "method"), "class")), "method"), "html", null, true);
            if ((!((array_key_exists("classonly", $context)) ? (_twig_default_filter($this->getContext($context, "classonly"), false)) : (false)))) {
                echo "::";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "method"), "name"), "html", null, true);
            }
            // line 20
            echo "</a>";
        } catch(Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ob_get_clean();
    }

    // line 23
    public function getproperty_link($property = null, $attributes = null, $absolute = null, $classonly = null)
    {
        $context = $this->env->mergeGlobals(array(
            "property" => $property,
            "attributes" => $attributes,
            "absolute" => $absolute,
            "classonly" => $classonly,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 24
            echo "<a href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('sami')->pathForProperty($context, $this->getContext($context, "property")), "html", null, true);
            echo "\"";
            echo twig_escape_filter($this->env, $this->getAttribute($this, "attributes", array(0 => $this->getContext($context, "attributes")), "method"), "html", null, true);
            echo ">";
            // line 25
            echo twig_escape_filter($this->env, $this->getAttribute($this, "abbr_class", array(0 => $this->getAttribute($this->getContext($context, "property"), "class")), "method"), "html", null, true);
            if ((!((array_key_exists("classonly", $context)) ? (_twig_default_filter($this->getContext($context, "classonly"), true)) : (true)))) {
                echo "#";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "property"), "name"), "html", null, true);
            }
            // line 26
            echo "</a>";
        } catch(Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ob_get_clean();
    }

    // line 29
    public function gethint_link($hints = null, $attributes = null)
    {
        $context = $this->env->mergeGlobals(array(
            "hints" => $hints,
            "attributes" => $attributes,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 30
            if ($this->getContext($context, "hints")) {
                // line 31
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getContext($context, "hints"));
                $context['loop'] = array(
                  'parent' => $context['_parent'],
                  'index0' => 0,
                  'index'  => 1,
                  'first'  => true,
                );
                if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
                    $length = count($context['_seq']);
                    $context['loop']['revindex0'] = $length - 1;
                    $context['loop']['revindex'] = $length;
                    $context['loop']['length'] = $length;
                    $context['loop']['last'] = 1 === $length;
                }
                foreach ($context['_seq'] as $context["_key"] => $context["hint"]) {
                    // line 32
                    if ($this->getAttribute($this->getContext($context, "hint"), "class")) {
                        // line 33
                        echo "                <span class=\"label label-hint label-info\">";
                        echo twig_escape_filter($this->env, $this->getAttribute($this, "class_link", array(0 => $this->getAttribute($this->getContext($context, "hint"), "name")), "method"), "html", null, true);
                        echo "</span>";
                    } elseif ($this->getAttribute($this->getContext($context, "hint"), "name")) {
                        // line 35
                        echo "                <span class=\"label label-hint label-warning\">";
                        echo $this->env->getExtension('sami')->abbrClass($this->getAttribute($this->getContext($context, "hint"), "name"));
                        echo "</span>";
                    }
                    // line 37
                    if ($this->getAttribute($this->getContext($context, "hint"), "array")) {
                        echo "[]";
                    }
                    // line 38
                    if ((!$this->getAttribute($this->getContext($context, "loop"), "last"))) {
                        echo "|";
                    }
                    ++$context['loop']['index0'];
                    ++$context['loop']['index'];
                    $context['loop']['first'] = false;
                    if (isset($context['loop']['length'])) {
                        --$context['loop']['revindex0'];
                        --$context['loop']['revindex'];
                        $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                    }
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['hint'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
            }
        } catch(Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ob_get_clean();
    }

    // line 43
    public function getabbr_class($class = null, $absolute = null)
    {
        $context = $this->env->mergeGlobals(array(
            "class" => $class,
            "absolute" => $absolute,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 44
            echo "<abbr title=\"";
            echo twig_escape_filter($this->env, $this->getContext($context, "class"), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, ((((array_key_exists("absolute", $context)) ? (_twig_default_filter($this->getContext($context, "absolute"), false)) : (false))) ? ($this->getContext($context, "class")) : ($this->getAttribute($this->getContext($context, "class"), "shortname"))), "html", null, true);
            echo "</abbr>";
        } catch(Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ob_get_clean();
    }

    // line 47
    public function getmethod_parameters_signature($method = null)
    {
        $context = $this->env->mergeGlobals(array(
            "method" => $method,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 48
            $context["__internal_319861065c24c576122107de1e6a03d6_1"] = $this->env->loadTemplate("macros.twig");
            // line 49
            echo "(";
            // line 50
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getContext($context, "method"), "parameters"));
            $context['loop'] = array(
              'parent' => $context['_parent'],
              'index0' => 0,
              'index'  => 1,
              'first'  => true,
            );
            if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
                $length = count($context['_seq']);
                $context['loop']['revindex0'] = $length - 1;
                $context['loop']['revindex'] = $length;
                $context['loop']['length'] = $length;
                $context['loop']['last'] = 1 === $length;
            }
            foreach ($context['_seq'] as $context["_key"] => $context["parameter"]) {
                // line 51
                if ($this->getAttribute($this->getContext($context, "parameter"), "hashint")) {
                    echo $context["__internal_319861065c24c576122107de1e6a03d6_1"]->gethint_link($this->getAttribute($this->getContext($context, "parameter"), "hint"));
                    echo " ";
                }
                // line 52
                echo "\$";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "parameter"), "name"), "html", null, true);
                // line 53
                if ($this->getAttribute($this->getContext($context, "parameter"), "default")) {
                    echo " = ";
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "parameter"), "default"), "html", null, true);
                }
                // line 54
                if ((!$this->getAttribute($this->getContext($context, "loop"), "last"))) {
                    echo ", ";
                }
                ++$context['loop']['index0'];
                ++$context['loop']['index'];
                $context['loop']['first'] = false;
                if (isset($context['loop']['length'])) {
                    --$context['loop']['revindex0'];
                    --$context['loop']['revindex'];
                    $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['parameter'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 56
            echo ")";
        } catch(Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ob_get_clean();
    }

    // line 59
    public function getclass_href($class = null)
    {
        $context = $this->env->mergeGlobals(array(
            "class" => $class,
        ));

        $blocks = array();

        ob_start();
        try {
            echo "/CalendR/api/";
            echo twig_escape_filter($this->env, strtr($this->getContext($context, "class"), "\\", "/"), "html", null, true);
            echo ".html";
        } catch(Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ob_get_clean();
    }

    public function getTemplateName()
    {
        return "macros.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  371 => 59,  360 => 56,  344 => 54,  339 => 53,  336 => 52,  331 => 51,  314 => 50,  312 => 49,  310 => 48,  299 => 47,  284 => 44,  272 => 43,  246 => 38,  242 => 37,  237 => 35,  232 => 33,  230 => 32,  213 => 31,  211 => 30,  199 => 29,  188 => 26,  182 => 25,  176 => 24,  162 => 23,  151 => 20,  145 => 19,  139 => 18,  125 => 17,  112 => 14,  110 => 13,  103 => 11,  99 => 9,  95 => 8,  88 => 7,  86 => 6,  73 => 5,  51 => 2,  40 => 1,  29 => 42,  26 => 28,  23 => 22,  20 => 16,  75 => 23,  68 => 22,  66 => 21,  61 => 19,  56 => 18,  49 => 14,  43 => 12,  38 => 9,  35 => 58,  32 => 46,  27 => 6,  24 => 5,  22 => 3,  19 => 2,  17 => 4,);
    }
}
