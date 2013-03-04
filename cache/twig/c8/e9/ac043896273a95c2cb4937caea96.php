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
            if ((((!$this->env->getExtension('calendr_doc')->contains($this->getContext($context, "class"), "Exception")) && (!$this->env->getExtension('calendr_doc')->contains($this->getContext($context, "class"), "Extension"))) && (!$this->env->getExtension('calendr_doc')->contains($this->getContext($context, "class"), "Bundle")))) {
                // line 8
                echo "        ";
                if ((($this->getContext($context, "current_namespace") != "") && ($this->getContext($context, "current_namespace") != $this->getAttribute($this->getContext($context, "class"), "namespace")))) {
                    // line 9
                    echo "            ";
                    if (($this->getContext($context, "current_namespace") != "Finite")) {
                        // line 10
                        echo "                    </ul>
                </li>
            ";
                    }
                    // line 13
                    echo "            <li class=\"namespace";
                    echo (("{%if page.current_namespace == \"" . $this->getAttribute($this->getContext($context, "class"), "namespace")) . "\"%} active{%endif%}");
                    echo "\">
                <a class=\"dropdown-toggle\" href=\"#\" data-toggle=\"dropdown\">
                    ";
                    // line 15
                    echo $this->env->getExtension('calendr_doc')->removeVendor($this->getAttribute($this->getContext($context, "class"), "namespace"));
                    echo "
                </a>
                <ul class=\"dropdown-menu\">
        ";
                }
                // line 19
                echo "        <li";
                echo (("{%if page.current_class == \"" . $this->getAttribute($this->getContext($context, "class"), "name")) . "\"%} class=\"active\"{%endif%}");
                echo ">
            ";
                // line 20
                echo $context["__internal_c8e9ac043896273a95c2cb4937caea96_1"]->getclass_link($this->getContext($context, "class"), array(), true);
                echo "
        </li>
        ";
                // line 22
                $context["current_namespace"] = $this->getAttribute($this->getContext($context, "class"), "namespace");
                // line 23
                echo "    ";
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['class'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 24
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
        return array (  75 => 24,  68 => 23,  66 => 22,  61 => 20,  56 => 19,  49 => 15,  43 => 13,  38 => 10,  35 => 9,  32 => 8,  27 => 6,  24 => 5,  22 => 3,  19 => 2,  17 => 1,);
    }
}
