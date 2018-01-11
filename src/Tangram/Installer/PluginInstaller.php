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

namespace Tangram\Installer;

use Tangram\Tangram;
use Tangram\IO\IOInterface;
use Tangram\Repository\InstalledRepositoryInterface;
use Tangram\Package\PackageInterface;

/**
 * Installer for plugin packages
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author Nils Adermann <naderman@naderman.de>
 */
class PluginInstaller extends LibraryInstaller
{
    private $installationManager;

    /**
     * Initializes Plugin installer.
     *
     * @param IOInterface $io
     * @param Tangram    $tangram
     * @param string      $type
     */
    public function __construct(IOInterface $io, Tangram $tangram, $type = 'library')
    {
        parent::__construct($io, $tangram, 'composer-plugin');
        $this->installationManager = $tangram->getInstallationManager();
    }

    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        return $packageType === 'composer-plugin' || $packageType === 'composer-installer';
    }

    /**
     * {@inheritDoc}
     */
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $extra = $package->getExtra();
        if (empty($extra['class'])) {
            throw new \UnexpectedValueException('Error while installing '.$package->getPrettyName().', composer-plugin packages should have a class defined in their extra key to be usable.');
        }

        parent::install($repo, $package);
        try {
            $this->tangram->getPluginManager()->registerPackage($package, true);
        } catch (\Exception $e) {
            // Rollback installation
            $this->io->writeError('Plugin installation failed, rolling back');
            parent::uninstall($repo, $package);
            throw $e;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
    {
        $extra = $target->getExtra();
        if (empty($extra['class'])) {
            throw new \UnexpectedValueException('Error while installing '.$target->getPrettyName().', composer-plugin packages should have a class defined in their extra key to be usable.');
        }

        parent::update($repo, $initial, $target);
        $this->tangram->getPluginManager()->registerPackage($target, true);
    }
}
