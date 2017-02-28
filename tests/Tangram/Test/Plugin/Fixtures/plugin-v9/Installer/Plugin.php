<?php

declare(strict_types = 1);

namespace Installer;

use Tangram\Composer;
use Tangram\IO\IOInterface;
use Tangram\Plugin\PluginInterface;

class Plugin implements PluginInterface
{
    public $version = 'installer-v9';

    public function activate(Composer $composer, IOInterface $io)
    {
    }
}
