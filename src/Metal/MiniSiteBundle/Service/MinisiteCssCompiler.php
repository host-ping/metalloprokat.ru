<?php

namespace Metal\MiniSiteBundle\Service;

use Symfony\Component\Templating\EngineInterface;

class MinisiteCssCompiler
{
    /**
     * @var EngineInterface
     */
    protected $engine;

    public function __construct(EngineInterface $engine, $webDir, $minisiteCompiledThemesPath)
    {
        $this->engine = $engine;
        $this->webDir = $webDir;
        $this->minisiteCompiledThemesPath = $minisiteCompiledThemesPath;
    }

    public static function fixBehaviorsUrl($css)
    {
        //TODO: переписать это уродство. Мы как-то через контейнер должны обрабатывать это
        return str_replace('/markup/', '/bundles/metalproject/', $css);
    }

    public function compileCss(array $colors, $filename)
    {
        $cachedCssFile = $this->webDir.'/'.$this->minisiteCompiledThemesPath.'/'.$filename.'.css';

        if (!is_dir(dirname($cachedCssFile))) {
            @mkdir(dirname($cachedCssFile));
        }
        $compiledCss = $this->engine->render(
            '@MetalProject/../public/css/minisite-themes/theme-template.css.twig',
            compact('colors')
        );

        $compiledCss = self::fixBehaviorsUrl($compiledCss);
        file_put_contents($cachedCssFile, $compiledCss);

        return $compiledCss;
    }
}
