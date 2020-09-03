<?php
error_reporting(-1);
$loader = require_once './vendor/autoload.php';

$options = array(
    'templates_dir' => 'markup/html',
    'css_themes_dir' => 'markup/css/minisite-themes'
);

$loader = new Twig_Loader_Filesystem(__DIR__);
$loader->addPath('system', 'system');
$loader->addPath($options['templates_dir'], 'markup');
$loader->addPath($options['css_themes_dir'], 'css');
$twig = new Twig_Environment($loader, array(
    'cache' => './cache',
    'debug' => true,
));

$twig->addFunction(new Twig_SimpleFunction('path', function($path) {
        return '?render='.$path;
    }));

$templates = array();
$iterator = \Symfony\Component\Finder\Finder::create()
    ->in(__DIR__.'/'.$options['templates_dir'])
    ->notPath('#(^|/)\_#')
    ->notPath('partial')
    ->notName('_*')
    ->name('*.html.twig')
    ->sortByType()
;

//$tree = new  RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__.'/markup/html'), RecursiveIteratorIterator::SELF_FIRST);
//var_dump(iterator_to_array($tree, true));exit;
$tree = array();
foreach ($iterator as $child) {
    /* @var $child \Symfony\Component\Finder\SplFileInfo */
    $key = str_replace('\\', '/', $child->getRelativePathname());
    $keyArray = explode('/', $key);
    $n = count($keyArray) - 1;

    $tmp =& $tree;
    foreach ($keyArray as $i => $subkey) {
        if ($i < $n) {
            $tmp =& $tmp[$subkey];
        } else {
            $tmp[] = array(
                'leaf'  => true,
                'title' => $subkey,
                'path'  => $key,
            );
        }
    }
}
//var_dump($readyTree, $templates);exit;
$render = isset($_GET['render']) ? $_GET['render'] : null;
$renderCss = isset($_GET['render_css']) ? $_GET['render_css'] : null;
if ($renderCss) {
    $content = $twig->render('@css/'.$renderCss);
    header('Content-Type: text/css; charset=utf-8');
} else {
    if (!$render) {
        $content = $twig->render('@system/index.html.twig', compact('tree'));
    } else {
        $data = array(
            'query' => $_GET,
        );

        $content = $twig->render('@markup/'.$render, $data);
    }
    header('Content-Type: text/html; charset=utf-8');
}

echo $content;
