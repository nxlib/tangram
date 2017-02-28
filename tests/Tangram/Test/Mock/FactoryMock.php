<?php
/*
 * This file is part of Composer.
 *
 * (c) Nils Adermann <naderman@naderman.de>
 *     Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tangram\Test\Mock;

use Tangram\Composer;
use Tangram\Config;
use Tangram\Factory;
use Tangram\Repository\RepositoryManager;
use Tangram\Repository\WritableRepositoryInterface;
use Tangram\Installer;
use Tangram\IO\IOInterface;
use Tangram\TestCase;

class FactoryMock extends Factory
{
    public static function createConfig(IOInterface $io = null, $cwd = null)
    {
        $config = new Config(true, $cwd);

        $config->merge(array(
            'config' => array('home' => TestCase::getUniqueTmpDirectory()),
            'repositories' => array('packagist' => false),
        ));

        return $config;
    }

    protected function addLocalRepository(IOInterface $io, RepositoryManager $rm, $vendorDir)
    {
    }

    protected function createInstallationManager()
    {
        return new InstallationManagerMock;
    }

    protected function createDefaultInstallers(Installer\InstallationManager $im, Composer $composer, IOInterface $io)
    {
    }

    protected function purgePackages(WritableRepositoryInterface $repo, Installer\InstallationManager $im)
    {
    }
}
