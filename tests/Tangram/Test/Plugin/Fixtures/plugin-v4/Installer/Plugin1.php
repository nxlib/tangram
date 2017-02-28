<?php

namespace Installer;

use Tangram\Composer;
use Tangram\IO\IOInterface;
use Tangram\Plugin\PluginInterface;

class Plugin1 implements PluginInterface
{
    public $name = 'plugin1';
    public $version = 'installer-v4';

    public function activate(Composer $composer, IOInterface $io)
    {
    }
}
