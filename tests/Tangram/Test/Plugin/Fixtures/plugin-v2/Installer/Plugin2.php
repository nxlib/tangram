<?php

namespace Installer;

use Tangram\Composer;
use Tangram\IO\IOInterface;
use Tangram\Plugin\PluginInterface;

class Plugin2 implements PluginInterface
{
    public $version = 'installer-v2';

    public function activate(Composer $composer, IOInterface $io)
    {
    }
}
