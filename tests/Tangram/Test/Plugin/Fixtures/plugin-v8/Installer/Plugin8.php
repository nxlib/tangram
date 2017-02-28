<?php

namespace Installer;

use Tangram\Composer;
use Tangram\IO\IOInterface;
use Tangram\Plugin\PluginInterface;
use Tangram\Plugin\Capable;

class Plugin8 implements PluginInterface, Capable
{
    public $version = 'installer-v8';

    public function activate(Composer $composer, IOInterface $io)
    {
    }

    public function getCapabilities()
    {
        return array(
            'Tangram\Plugin\Capability\CommandProvider' => 'Installer\CommandProvider',
        );
    }
}
