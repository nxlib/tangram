<?php

namespace Tangram;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

class Compiler
{
    private $version;

    public function compile($pharFile = 'tangram.phar')
    {
        if (file_exists($pharFile)) {
            unlink($pharFile);
        }

        $process = new Process('git log --pretty="%h" -n1 HEAD');
        if ($process->run() != 0) {
            throw new \RuntimeException('The git binary cannot be found.');
        }
        $this->version = trim($process->getOutput());

        $phar = new \Phar($pharFile, 0, 'tangram.phar');
        $phar->setSignatureAlgorithm(\Phar::SHA1);

        $phar->startBuffering();

        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->notName('Compiler.php')
            ->in(__DIR__.'/..')
        ;

        foreach ($finder as $file) {
            $this->addFile($phar, $file);
        }

        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->in(__DIR__.'/../../vendor/symfony/')
        ;

        foreach ($finder as $file) {
            $this->addFile($phar, $file);
        }
        $vendorPath = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'vendor';
        $composerPath = $vendorPath.DIRECTORY_SEPARATOR.'composer';

        $this->addFile($phar, new \SplFileInfo($vendorPath.DIRECTORY_SEPARATOR.'autoload.php'));
        $this->addFile($phar, new \SplFileInfo($composerPath.DIRECTORY_SEPARATOR.'ClassLoader.php'));
        $this->addFile($phar, new \SplFileInfo($composerPath.DIRECTORY_SEPARATOR.'autoload_static.php'));
        $this->addFile($phar, new \SplFileInfo($composerPath.DIRECTORY_SEPARATOR.'autoload_real.php'));
        $this->addFile($phar, new \SplFileInfo($composerPath.DIRECTORY_SEPARATOR.'autoload_psr4.php'));
        $this->addFile($phar, new \SplFileInfo($composerPath.DIRECTORY_SEPARATOR.'autoload_namespaces.php'));
        $this->addFile($phar, new \SplFileInfo($composerPath.DIRECTORY_SEPARATOR.'autoload_files.php'));
        $this->addFile($phar, new \SplFileInfo($composerPath.DIRECTORY_SEPARATOR.'autoload_classmap.php'));
        $this->addComposerBin($phar);

        // Stubs
        $phar->setStub($this->getStub());

        $phar->stopBuffering();

        // disabled for interoperability with systems without gzip ext
        // $phar->compressFiles(\Phar::GZ);

        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../LICENSE'), false);

        unset($phar);
    }

    private function addFile($phar, $file, $strip = true)
    {
        $path = str_replace(dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR, '', $file->getRealPath());

        if ($strip) {
            $content = php_strip_whitespace($file);
        } else {
            $content = "\n".file_get_contents($file)."\n";
        }

        $content = str_replace('@package_version@', $this->version, $content);

        $phar->addFromString($path, $content);
    }

    private function addComposerBin($phar)
    {
        $content = file_get_contents(__DIR__.'/../../bin/tangram');
        $content = preg_replace('{^#!/usr/bin/env php\s*}', '', $content);
        $phar->addFromString('bin/tangram', $content);
    }

    private function getStub()
    {
        return <<<'EOF'
#!/usr/bin/env php
<?php
/*
 * This file is part of Tangram.
 *
 * (c) Garming Lau <garming@msn.com>
 *
 * For the full copyright and license information, please view
 * the license that is located at the bottom of this file.
 */

Phar::mapPhar('tangram.phar');

require 'phar://tangram.phar/bin/tangram';

__HALT_COMPILER();
EOF;
    }
}