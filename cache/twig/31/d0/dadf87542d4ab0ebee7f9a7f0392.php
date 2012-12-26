<?php

/* pages/class.twig */
class __TwigTemplate_31d0dadf87542d4ab0ebee7f9a7f0392 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->blocks = array(
            'current_class' => array($this, 'block_current_class'),
            'current_namespace' => array($this, 'block_current_namespace'),
            'title' => array($this, 'block_title'),
            'content' => array($this, 'block_content'),
            'class_signature' => array($this, 'block_class_signature'),
            'method_signature' => array($this, 'block_method_signature'),
            'method_parameters_signature' => array($this, 'block_method_parameters_signature'),
            'parameters' => array($this, 'block_parameters'),
            'return' => array($this, 'block_return'),
            'exceptions' => array($this, 'block_exceptions'),
            'see' => array($this, 'block_see'),
            'constants' => array($this, 'block_constants'),
            'properties' => array($this, 'block_properties'),
            'methods' => array($this, 'block_methods'),
            'methods_details' => array($this, 'block_methods_details'),
            'method' => array($this, 'block_method'),
        );
    }

    protected function doGetParent(array $context)
    {
        return $this->env->resolveTemplate($this->getContext($context, "page_layout"));
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 3
        $context["__internal_31d0dadf87542d4ab0ebee7f9a7f0392_1"] = $this->env->loadTemplate("macros.twig");
        $this->getParent($context)->display($context, array_merge($this->blocks, $blocks));
    }

    // line 11
    public function block_current_class($context, array $blocks = array())
    {
        echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "class"), "name"), "html", null, true);
    }

    // line 12
    public function block_current_namespace($context, array $blocks = array())
    {
        echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "class"), "namespace"), "html", null, true);
    }

    // line 14
    public function block_title($context, array $blocks = array())
    {
        echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "class"), "name"), "html", null, true);
        echo " | ";
        $this->displayParentBlock("title", $context, $blocks);
    }

    // line 16
    public function block_content($context, array $blocks = array())
    {
        // line 17
        echo "    <div class=\"hero-unit\">
        <h1>
            <span class=\"class-info\">
                <small>";
        // line 20
        if ($this->getAttribute($this->getContext($context, "class"), "interface")) {
            echo "Interface";
        } else {
            echo "Class";
        }
        echo "</small>
                <small>";
        // line 21
        echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "class"), "namespace"), "html", null, true);
        echo "\\</small>
            </span>
            <span class=\"class-name\">";
        // line 23
        echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "class"), "shortname"), "html", null, true);
        echo "</span>
        </h1>

        ";
        // line 26
        if (($this->getAttribute($this->getContext($context, "class"), "shortdesc") || $this->getAttribute($this->getContext($context, "class"), "longdesc"))) {
            // line 27
            echo "            <div class=\"class-desc\">
                <p>";
            // line 28
            echo $this->env->getExtension('calendr_doc')->markdownify($this->getAttribute($this->getContext($context, "class"), "shortdesc"));
            echo "</p>
                <p>";
            // line 29
            echo $this->env->getExtension('calendr_doc')->markdownify($this->getAttribute($this->getContext($context, "class"), "longdesc"));
            echo "</p>
                ";
            // line 30
            if (($this->getAttribute($this->getContext($context, "class"), "parent") || $this->getAttribute($this->getContext($context, "class"), "interfaces"))) {
                echo "<p>";
            }
            // line 31
            echo "                    ";
            if ($this->getAttribute($this->getContext($context, "class"), "parent")) {
                // line 32
                echo "                        <br />Inherits from: ";
                echo $context["__internal_31d0dadf87542d4ab0ebee7f9a7f0392_1"]->getclass_link($this->getAttribute($this->getContext($context, "class"), "parent"));
                echo "
                    ";
            }
            // line 34
            echo "
                    ";
            // line 35
            if ($this->getAttribute($this->getContext($context, "class"), "interfaces")) {
                // line 36
                echo "                        <br />
                        Implements:
                        ";
                // line 38
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getContext($context, "class"), "interfaces"));
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
                foreach ($context['_seq'] as $context["_key"] => $context["interface"]) {
                    // line 39
                    echo "                            ";
                    echo $context["__internal_31d0dadf87542d4ab0ebee7f9a7f0392_1"]->getclass_link($this->getContext($context, "interface"));
                    echo (($this->getAttribute($this->getContext($context, "loop"), "last")) ? ("") : (", "));
                    echo "
                        ";
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
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['interface'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 41
                echo "                    ";
            }
            // line 42
            echo "                ";
            if (($this->getAttribute($this->getContext($context, "class"), "parent") || $this->getAttribute($this->getContext($context, "class"), "interfaces"))) {
                echo "</p>";
            }
            // line 43
            echo "            </div>
        ";
        }
        // line 45
        echo "    </div>


    ";
        // line 48
        if ($this->getContext($context, "constants")) {
            // line 49
            echo "        ";
            echo twig_escape_filter($this->env, $this->getAttribute($this, "section_title", array(0 => "Constants"), "method"), "html", null, true);
            echo "

        ";
            // line 51
            $this->displayBlock("constants", $context, $blocks);
            echo "
    ";
        }
        // line 53
        echo "
    ";
        // line 54
        if ($this->getContext($context, "properties")) {
            // line 55
            echo "        ";
            echo twig_escape_filter($this->env, $this->getAttribute($this, "section_title", array(0 => "Properties"), "method"), "html", null, true);
            echo "

        ";
            // line 57
            $this->displayBlock("properties", $context, $blocks);
            echo "
    ";
        }
        // line 59
        echo "
    ";
        // line 60
        if ($this->getContext($context, "methods")) {
            // line 61
            echo "        <h2>Methods</h2>

        ";
            // line 63
            $this->displayBlock("methods", $context, $blocks);
            echo "

        <h2>Details</h2>

        ";
            // line 67
            $this->displayBlock("methods_details", $context, $blocks);
            echo "
    ";
        }
    }

    // line 71
    public function block_class_signature($context, array $blocks = array())
    {
        // line 72
        if (((!$this->getAttribute($this->getContext($context, "class"), "interface")) && $this->getAttribute($this->getContext($context, "class"), "abstract"))) {
            echo "abstract ";
        }
        // line 73
        if ($this->getAttribute($this->getContext($context, "class"), "interface")) {
            echo "interface";
        } else {
            echo "class";
        }
        // line 74
        echo "    <strong>";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "class"), "shortname"), "html", null, true);
        echo "</strong>";
        // line 75
        if ($this->getAttribute($this->getContext($context, "class"), "parent")) {
            // line 76
            echo "        extends ";
            echo $context["__internal_31d0dadf87542d4ab0ebee7f9a7f0392_1"]->getclass_link($this->getAttribute($this->getContext($context, "class"), "parent"));
        }
        // line 78
        if ((twig_length_filter($this->env, $this->getAttribute($this->getContext($context, "class"), "interfaces")) > 0)) {
            // line 79
            echo "        implements
        ";
            // line 80
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getContext($context, "class"), "interfaces"));
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
            foreach ($context['_seq'] as $context["_key"] => $context["interface"]) {
                // line 81
                echo $context["__internal_31d0dadf87542d4ab0ebee7f9a7f0392_1"]->getclass_link($this->getContext($context, "interface"));
                // line 82
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
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['interface'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
        }
    }

    // line 87
    public function block_method_signature($context, array $blocks = array())
    {
        // line 88
        if ($this->getAttribute($this->getContext($context, "method"), "final")) {
            echo "<span class=\"label label-success\">final</span>";
        }
        // line 89
        echo "    ";
        if ($this->getAttribute($this->getContext($context, "method"), "abstract")) {
            echo "<span class=\"label label-success\">abstract</span>";
        }
        // line 90
        echo "    ";
        if ($this->getAttribute($this->getContext($context, "method"), "static")) {
            echo "<span class=\"label label-success\">static</span>";
        }
        // line 91
        echo "    ";
        if ($this->getAttribute($this->getContext($context, "method"), "public")) {
            echo "<span class=\"label label-success\">public</span>";
        }
        // line 92
        echo "    ";
        if ($this->getAttribute($this->getContext($context, "method"), "protected")) {
            echo "<span class=\"label label-success\">protected</span>";
        }
        // line 93
        echo "    ";
        if ($this->getAttribute($this->getContext($context, "method"), "private")) {
            echo "<span class=\"label label-success\">private</span>";
        }
        // line 94
        echo "    ";
        echo $context["__internal_31d0dadf87542d4ab0ebee7f9a7f0392_1"]->gethint_link($this->getAttribute($this->getContext($context, "method"), "hint"));
        echo "
    <strong>";
        // line 95
        echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "method"), "name"), "html", null, true);
        echo "</strong>";
        $this->displayBlock("method_parameters_signature", $context, $blocks);
    }

    // line 98
    public function block_method_parameters_signature($context, array $blocks = array())
    {
        // line 99
        $context["__internal_31d0dadf87542d4ab0ebee7f9a7f0392_2"] = $this->env->loadTemplate("macros.twig");
        // line 100
        echo $context["__internal_31d0dadf87542d4ab0ebee7f9a7f0392_2"]->getmethod_parameters_signature($this->getContext($context, "method"));
    }

    // line 103
    public function block_parameters($context, array $blocks = array())
    {
        // line 104
        echo "    <table class=\"table table-bordered table-stripped\">
        ";
        // line 105
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getContext($context, "method"), "parameters"));
        foreach ($context['_seq'] as $context["_key"] => $context["parameter"]) {
            // line 106
            echo "            <tr>
                <td>";
            // line 107
            if ($this->getAttribute($this->getContext($context, "parameter"), "hint")) {
                echo $context["__internal_31d0dadf87542d4ab0ebee7f9a7f0392_1"]->gethint_link($this->getAttribute($this->getContext($context, "parameter"), "hint"));
            }
            echo "</td>
                <td";
            // line 108
            if ((!$this->getAttribute($this->getContext($context, "parameter"), "shortdesc"))) {
                echo " colspan=\"2\" ";
            }
            echo ">\$";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "parameter"), "name"), "html", null, true);
            echo "</td>
                ";
            // line 109
            if ($this->getAttribute($this->getContext($context, "parameter"), "shortdesc")) {
                echo "<td>";
                echo nl2br(twig_escape_filter($this->env, $this->env->getExtension('sami')->parseDesc($context, $this->getAttribute($this->getContext($context, "parameter"), "shortdesc"), $this->getContext($context, "class")), "html", null, true));
                echo "</td>";
            }
            // line 110
            echo "            </tr>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['parameter'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 112
        echo "    </table>
";
    }

    // line 115
    public function block_return($context, array $blocks = array())
    {
        // line 116
        echo "    <table>
        <tr>
            <td>";
        // line 118
        echo $context["__internal_31d0dadf87542d4ab0ebee7f9a7f0392_1"]->gethint_link($this->getAttribute($this->getContext($context, "method"), "hint"));
        echo "</td>
            <td>";
        // line 119
        echo nl2br(twig_escape_filter($this->env, $this->env->getExtension('sami')->parseDesc($context, $this->getAttribute($this->getContext($context, "method"), "hintDesc"), $this->getContext($context, "class")), "html", null, true));
        echo "</td>
        </tr>
    </table>
";
    }

    // line 124
    public function block_exceptions($context, array $blocks = array())
    {
        // line 125
        echo "    <table>
        ";
        // line 126
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getContext($context, "method"), "exceptions"));
        foreach ($context['_seq'] as $context["_key"] => $context["exception"]) {
            // line 127
            echo "            <tr>
                <td>";
            // line 128
            echo $context["__internal_31d0dadf87542d4ab0ebee7f9a7f0392_1"]->getclass_link($this->getAttribute($this->getContext($context, "exception"), 0, array(), "array"));
            echo "</td>
                <td>";
            // line 129
            echo nl2br(twig_escape_filter($this->env, $this->env->getExtension('sami')->parseDesc($context, $this->getAttribute($this->getContext($context, "exception"), 1, array(), "array"), $this->getContext($context, "class")), "html", null, true));
            echo "</td>
            </tr>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['exception'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 132
        echo "    </table>
";
    }

    // line 135
    public function block_see($context, array $blocks = array())
    {
        // line 136
        echo "    <table>
        ";
        // line 137
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getContext($context, "method"), "tags", array(0 => "see"), "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["tag"]) {
            // line 138
            echo "            <tr>
                <td>";
            // line 139
            echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "tag"), 0, array(), "array"), "html", null, true);
            echo "</td>
                <td>";
            // line 140
            echo twig_escape_filter($this->env, twig_join_filter(twig_slice($this->env, $this->getContext($context, "tag"), 1, null), " "), "html", null, true);
            echo "</td>
            </tr>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tag'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 143
        echo "    </table>
";
    }

    // line 146
    public function block_constants($context, array $blocks = array())
    {
        // line 147
        echo "    <table>
        ";
        // line 148
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getContext($context, "constants"));
        foreach ($context['_seq'] as $context["_key"] => $context["constant"]) {
            // line 149
            echo "            <tr>
                <td>";
            // line 150
            echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "constant"), "name"), "html", null, true);
            echo "</td>
                <td class=\"last\">
                    <p><em>";
            // line 152
            echo nl2br(twig_escape_filter($this->env, $this->env->getExtension('sami')->parseDesc($context, $this->getAttribute($this->getContext($context, "constant"), "shortdesc"), $this->getContext($context, "class")), "html", null, true));
            echo "</em></p>
                    <p>";
            // line 153
            echo nl2br(twig_escape_filter($this->env, $this->env->getExtension('sami')->parseDesc($context, $this->getAttribute($this->getContext($context, "constant"), "longdesc"), $this->getContext($context, "class")), "html", null, true));
            echo "</p>
                </td>
            </tr>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['constant'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 157
        echo "    </table>
";
    }

    // line 160
    public function block_properties($context, array $blocks = array())
    {
        // line 161
        echo "    <table>
        ";
        // line 162
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getContext($context, "properties"));
        foreach ($context['_seq'] as $context["_key"] => $context["property"]) {
            // line 163
            echo "            <tr>
                <td class=\"type\" id=\"property_";
            // line 164
            echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "property"), "name"), "html", null, true);
            echo "\">
                    ";
            // line 165
            if ($this->getAttribute($this->getContext($context, "property"), "static")) {
                echo "static";
            }
            // line 166
            echo "                    ";
            if ($this->getAttribute($this->getContext($context, "property"), "protected")) {
                echo "protected";
            }
            // line 167
            echo "                    ";
            echo $context["__internal_31d0dadf87542d4ab0ebee7f9a7f0392_1"]->gethint_link($this->getAttribute($this->getContext($context, "property"), "hint"));
            echo "
                </td>
                <td>\$";
            // line 169
            echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "property"), "name"), "html", null, true);
            echo "</td>
                <td class=\"last\">";
            // line 170
            echo nl2br(twig_escape_filter($this->env, $this->env->getExtension('sami')->parseDesc($context, $this->getAttribute($this->getContext($context, "property"), "shortdesc"), $this->getContext($context, "class")), "html", null, true));
            echo "</td>
            </tr>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['property'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 173
        echo "    </table>
";
    }

    // line 176
    public function block_methods($context, array $blocks = array())
    {
        // line 177
        echo "    <table class=\"table\">
        ";
        // line 178
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getContext($context, "methods"));
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
        foreach ($context['_seq'] as $context["_key"] => $context["method"]) {
            // line 179
            echo "            <tr>
                <td class=\"type\">
                    ";
            // line 181
            if ($this->getAttribute($this->getContext($context, "method"), "static")) {
                // line 182
                echo "                        <span class=\"label label-success\">static</span>
                    ";
            }
            // line 184
            echo "                    ";
            echo $context["__internal_31d0dadf87542d4ab0ebee7f9a7f0392_1"]->gethint_link($this->getAttribute($this->getContext($context, "method"), "hint"));
            echo "
                </td>
                <td class=\"last\">
                    <a href=\"#method_";
            // line 187
            echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "method"), "name"), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "method"), "name"), "html", null, true);
            echo "</a>";
            $this->displayBlock("method_parameters_signature", $context, $blocks);
            echo "
                    <p>";
            // line 188
            echo nl2br(twig_escape_filter($this->env, $this->env->getExtension('sami')->parseDesc($context, $this->getAttribute($this->getContext($context, "method"), "shortdesc"), $this->getContext($context, "class")), "html", null, true));
            echo "</p>
                </td>
                <td>";
            // line 191
            if (($this->getAttribute($this->getContext($context, "method"), "class") != $this->getContext($context, "class"))) {
                // line 192
                echo "<small>from&nbsp;";
                echo $context["__internal_31d0dadf87542d4ab0ebee7f9a7f0392_1"]->getmethod_link($this->getContext($context, "method"), array(), false, true);
                echo "</small>";
            }
            // line 194
            echo "</td>
            </tr>
        ";
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
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['method'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 197
        echo "    </table>
";
    }

    // line 200
    public function block_methods_details($context, array $blocks = array())
    {
        // line 201
        echo "    ";
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getContext($context, "methods"));
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
        foreach ($context['_seq'] as $context["_key"] => $context["method"]) {
            // line 202
            echo "        ";
            $this->displayBlock("method", $context, $blocks);
            echo "
    ";
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
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['method'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
    }

    // line 206
    public function block_method($context, array $blocks = array())
    {
        // line 207
        echo "    <h3 id=\"method_";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "method"), "name"), "html", null, true);
        echo "\">
        <code class=\"heading-code\">";
        // line 208
        $this->displayBlock("method_signature", $context, $blocks);
        echo "</code>
    </h3>
    <div class=\"details method-details\">
        <p>";
        // line 211
        echo $this->env->getExtension('calendr_doc')->markdownify($this->getAttribute($this->getContext($context, "method"), "shortdesc"));
        echo "</p>
        <p>";
        // line 212
        echo $this->env->getExtension('calendr_doc')->markdownify($this->getAttribute($this->getContext($context, "method"), "longdesc"));
        echo "</p>
        <table class=\"tags table method-detail\">
            ";
        // line 214
        if ($this->getAttribute($this->getContext($context, "method"), "parameters")) {
            // line 215
            echo "                <tr>
                    <th>Parameters</th>
                    <td>";
            // line 217
            $this->displayBlock("parameters", $context, $blocks);
            echo "</td>
                </tr>
            ";
        }
        // line 220
        echo "
            ";
        // line 221
        if (($this->getAttribute($this->getContext($context, "method"), "hintDesc") || $this->getAttribute($this->getContext($context, "method"), "hint"))) {
            // line 222
            echo "                <tr>
                    <th>Return Value</th>
                    <td>";
            // line 224
            $this->displayBlock("return", $context, $blocks);
            echo "</td>
                </tr>
            ";
        }
        // line 227
        echo "
            ";
        // line 228
        if ($this->getAttribute($this->getContext($context, "method"), "exceptions")) {
            // line 229
            echo "                <tr>
                    <th>Exceptions</th>
                    <td>";
            // line 231
            $this->displayBlock("exceptions", $context, $blocks);
            echo "</td>
                </tr>
            ";
        }
        // line 234
        echo "
            ";
        // line 235
        if ($this->getAttribute($this->getContext($context, "method"), "tags", array(0 => "see"), "method")) {
            // line 236
            echo "                <tr>
                    <th>See also</th>
                    <td>";
            // line 238
            $this->displayBlock("see", $context, $blocks);
            echo "<td>
                </tr>
            ";
        }
        // line 241
        echo "        </table>
    </div>
";
    }

    // line 5
    public function getsection_title($title = null)
    {
        $context = $this->env->mergeGlobals(array(
            "title" => $title,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 6
            echo "    <div class=\"page-header\">
        <h2>";
            // line 7
            echo twig_escape_filter($this->env, $this->getContext($context, "title"), "html", null, true);
            echo "</h2>
    </div>
";
        } catch(Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ob_get_clean();
    }

    public function getTemplateName()
    {
        return "pages/class.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  779 => 7,  776 => 6,  765 => 5,  759 => 241,  753 => 238,  749 => 236,  747 => 235,  744 => 234,  738 => 231,  734 => 229,  732 => 228,  729 => 227,  723 => 224,  719 => 222,  717 => 221,  714 => 220,  708 => 217,  704 => 215,  702 => 214,  697 => 212,  693 => 211,  687 => 208,  682 => 207,  679 => 206,  660 => 202,  642 => 201,  639 => 200,  634 => 197,  618 => 194,  613 => 192,  611 => 191,  606 => 188,  598 => 187,  591 => 184,  587 => 182,  585 => 181,  581 => 179,  564 => 178,  561 => 177,  558 => 176,  553 => 173,  544 => 170,  540 => 169,  534 => 167,  529 => 166,  525 => 165,  521 => 164,  518 => 163,  514 => 162,  511 => 161,  508 => 160,  503 => 157,  493 => 153,  489 => 152,  484 => 150,  481 => 149,  477 => 148,  474 => 147,  471 => 146,  466 => 143,  457 => 140,  453 => 139,  450 => 138,  446 => 137,  443 => 136,  440 => 135,  435 => 132,  426 => 129,  422 => 128,  419 => 127,  415 => 126,  412 => 125,  409 => 124,  401 => 119,  397 => 118,  393 => 116,  390 => 115,  385 => 112,  378 => 110,  372 => 109,  364 => 108,  358 => 107,  355 => 106,  351 => 105,  348 => 104,  345 => 103,  341 => 100,  339 => 99,  336 => 98,  330 => 95,  325 => 94,  320 => 93,  315 => 92,  310 => 91,  305 => 90,  300 => 89,  296 => 88,  293 => 87,  274 => 82,  272 => 81,  255 => 80,  252 => 79,  250 => 78,  246 => 76,  244 => 75,  240 => 74,  234 => 73,  230 => 72,  227 => 71,  220 => 67,  213 => 63,  209 => 61,  207 => 60,  204 => 59,  199 => 57,  193 => 55,  191 => 54,  188 => 53,  183 => 51,  177 => 49,  175 => 48,  170 => 45,  166 => 43,  161 => 42,  158 => 41,  140 => 39,  123 => 38,  119 => 36,  117 => 35,  114 => 34,  108 => 32,  105 => 31,  101 => 30,  97 => 29,  93 => 28,  90 => 27,  88 => 26,  82 => 23,  77 => 21,  69 => 20,  64 => 17,  61 => 16,  53 => 14,  47 => 12,  41 => 11,  36 => 3,);
    }
}
