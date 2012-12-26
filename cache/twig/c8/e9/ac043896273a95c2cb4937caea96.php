<?php

/* pages/classes.twig */
class __TwigTemplate_c8e9ac043896273a95c2cb4937caea96 extends Twig_Template
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
        // line 1
        $context["__internal_c8e9ac043896273a95c2cb4937caea96_1"] = $this->env->loadTemplate("macros.twig");
        // line 2
        echo "
";
        // line 3
        $context["current_namespace"] = "";
        // line 5
        echo "<ul class=\"dropdown-menu\" id=\"api-menu\">
    ";
        // line 6
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->env->getExtension('calendr_doc')->namespaceSort($this->getContext($context, "classes")));
        foreach ($context['_seq'] as $context["_key"] => $context["class"]) {
            if (((!$this->env->getExtension('calendr_doc')->contains($this->getContext($context, "class"), "Exception")) && (!$this->env->getExtension('calendr_doc')->contains($this->getContext($context, "class"), "Extension")))) {
                // line 7
                echo "        ";
                if ((($this->getContext($context, "current_namespace") != "") && ($this->getContext($context, "current_namespace") != $this->getAttribute($this->getContext($context, "class"), "namespace")))) {
                    // line 8
                    echo "            ";
                    if (($this->getContext($context, "current_namespace") != "CalendR")) {
                        // line 9
                        echo "                    </ul>
                </li>
            ";
                    }
                    // line 12
                    echo "            <li class=\"namespace";
                    echo (("{%if page.current_namespace == \"" . $this->getAttribute($this->getContext($context, "class"), "namespace")) . "\"%} active{%endif%}");
                    echo "\">
                <a class=\"dropdown-toggle\" href=\"#\" data-toggle=\"dropdown\">
                    ";
                    // line 14
                    echo $this->env->getExtension('calendr_doc')->removeVendor($this->getAttribute($this->getContext($context, "class"), "namespace"));
                    echo "
                </a>
                <ul class=\"dropdown-menu\">
        ";
                }
                // line 18
                echo "        <li";
                echo (("{%if page.current_class == \"" . $this->getAttribute($this->getContext($context, "class"), "name")) . "\"%} class=\"active\"{%endif%}");
                echo ">
            ";
                // line 19
                echo $context["__internal_c8e9ac043896273a95c2cb4937caea96_1"]->getclass_link($this->getContext($context, "class"), array(), true);
                echo "
        </li>
        ";
                // line 21
                $context["current_namespace"] = $this->getAttribute($this->getContext($context, "class"), "namespace");
                // line 22
                echo "    ";
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['class'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 23
        echo "</ul>
";
    }

    public function getTemplateName()
    {
        return "pages/classes.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  75 => 23,  68 => 22,  66 => 21,  61 => 19,  56 => 18,  49 => 14,  43 => 12,  38 => 9,  35 => 8,  32 => 7,  27 => 6,  24 => 5,  22 => 3,  19 => 2,  17 => 1,);
    }
}
