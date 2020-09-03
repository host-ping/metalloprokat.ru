<?php

/* @system/index.html.twig */
class __TwigTemplate_1ac529c08582fccbad59a39a23eccc0e extends Twig_Template
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
        // line 15
        echo "
";
        // line 16
        echo $this->getAttribute($this, "renderTree", array(0 => (isset($context["tree"]) ? $context["tree"] : null)), "method");
        echo "
";
    }

    // line 1
    public function getrenderTree($_tree = null)
    {
        $context = $this->env->mergeGlobals(array(
            "tree" => $_tree,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 2
            echo "    <ul>
        ";
            // line 3
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["tree"]) ? $context["tree"] : null));
            foreach ($context['_seq'] as $context["key"] => $context["data"]) {
                // line 4
                echo "            <li>
                ";
                // line 5
                if ((!twig_test_empty($this->getAttribute((isset($context["data"]) ? $context["data"] : null), "leaf")))) {
                    // line 6
                    echo "                    <a href=\"?render=";
                    echo twig_escape_filter($this->env, $this->getAttribute((isset($context["data"]) ? $context["data"] : null), "path"), "html", null, true);
                    echo "\">";
                    echo twig_escape_filter($this->env, $this->getAttribute((isset($context["data"]) ? $context["data"] : null), "title"), "html", null, true);
                    echo "</a>
                ";
                } else {
                    // line 8
                    echo "                    ";
                    echo twig_escape_filter($this->env, (isset($context["key"]) ? $context["key"] : null), "html", null, true);
                    echo "
                    ";
                    // line 9
                    echo $this->getAttribute($this, "renderTree", array(0 => (isset($context["data"]) ? $context["data"] : null)), "method");
                    echo "
                ";
                }
                // line 11
                echo "            </li>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['key'], $context['data'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 13
            echo "    </ul>
";
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    public function getTemplateName()
    {
        return "@system/index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  76 => 13,  69 => 11,  64 => 9,  59 => 8,  51 => 6,  49 => 5,  46 => 4,  42 => 3,  39 => 2,  28 => 1,  22 => 16,  19 => 15,);
    }
}
