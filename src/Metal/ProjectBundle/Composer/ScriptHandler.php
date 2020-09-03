<?php

namespace Metal\ProjectBundle\Composer;

use Composer\Script\Event;
use Incenteev\ParameterHandler\Processor;

class ScriptHandler 
{
    public static function buildParameters(Event $event)
    {
        $processor = new Processor($event->getIO());
        $configs = json_decode(file_get_contents('parameters.json'), true);

        foreach ($configs as $config) {
            if (!is_array($config)) {
                throw new \InvalidArgumentException('The extra.incenteev-parameters setting must be an array of configuration objects.');
            }

            $processor->processFile($config);
        }
    }
}
