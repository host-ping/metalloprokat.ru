<?php

namespace Metal\ProjectBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\CacheClearCommand as BaseCommand;
use Symfony\Component\HttpKernel\KernelInterface;

class CacheClearCommand extends BaseCommand
{
    protected function configure()
    {
        parent::configure();
        $this->setName('metal:project:clear-cache');
    }

    /**
     * @param KernelInterface $parent
     * @param string          $namespace
     * @param string          $parentClass
     * @param string          $warmupDir
     *
     * @return KernelInterface
     */
    protected function getTempKernel(KernelInterface $parent, $namespace, $parentClass, $warmupDir)
    {
        /* @var $parent \AppKernel */

        $rootDir = $parent->getRootDir();
        // the temp kernel class name must have the same length than the real one
        // to avoid the many problems in serialized resources files
        $class = substr($parentClass, 0, -1).'_';
        // the temp kernel name must be changed too
        $name = substr($parent->getName(), 0, -1).'_';
        $code = <<<EOF
<?php

namespace $namespace
{
    class $class extends $parentClass
    {
        public function getCacheDir()
        {
            return '$warmupDir';
        }

        public function getName()
        {
            return '$name';
        }

        public function getRootDir()
        {
            return '$rootDir';
        }
    }
}
EOF;
        $this->getContainer()->get('filesystem')->mkdir($warmupDir);
        file_put_contents($file = $warmupDir.'/kernel.tmp', $code);
        require_once $file;
        @unlink($file);
        $class = "$namespace\\$class";

        return new $class($parent->getEnvironment(), $parent->isDebug(), $parent->getHostnamePackage());
    }
}
