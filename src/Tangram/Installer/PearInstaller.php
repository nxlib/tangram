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

use Tangram\IO\IOInterface;
use Tangram\Tangram;
use Tangram\Downloader\PearPackageExtractor;
use Tangram\Repository\InstalledRepositoryInterface;
use Tangram\Package\PackageInterface;
use Tangram\Util\Platform;
use Tangram\Util\Filesystem;

/**
 * Package installation manager.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class PearInstaller extends LibraryInstaller
{

  /**
   * Initializes library installer.
   *
   * @param IOInterface $io io instance
   * @param \Tangram\Tangram $tangram
   * @param string $type package type that this installer handles
   */
    public function __construct(IOInterface $io, Tangram $tangram, $type = 'pear-library')
    {
        $filesystem = new Filesystem();
        $binaryInstaller = new PearBinaryInstaller($io, rtrim($tangram->getConfig()->get('bin-dir'), '/'), rtrim($tangram->getConfig()->get('vendor-dir'), '/'), $tangram->getConfig()->get('bin-compat'), $filesystem, $this);

        parent::__construct($io, $tangram, $type, $filesystem, $binaryInstaller);
    }

    /**
     * {@inheritDoc}
     */
    public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
    {
        $this->uninstall($repo, $initial);
        $this->install($repo, $target);
    }

    protected function installCode(PackageInterface $package)
    {
        parent::installCode($package);

        $isWindows = Platform::isWindows();
        $php_bin = $this->binDir . ($isWindows ? '/composer-php.bat' : '/composer-php');

        if (!$isWindows) {
            $php_bin = '/usr/bin/env ' . $php_bin;
        }

        $installPath = $this->getInstallPath($package);
        $vars = array(
            'os' => $isWindows ? 'windows' : 'linux',
            'php_bin' => $php_bin,
            'pear_php' => $installPath,
            'php_dir' => $installPath,
            'bin_dir' => $installPath . '/bin',
            'data_dir' => $installPath . '/data',
            'version' => $package->getPrettyVersion(),
        );

        $packageArchive = $this->getInstallPath($package).'/'.pathinfo($package->getDistUrl(), PATHINFO_BASENAME);
        $pearExtractor = new PearPackageExtractor($packageArchive);
        $pearExtractor->extractTo($this->getInstallPath($package), array('php' => '/', 'script' => '/bin', 'data' => '/data'), $vars);

        $this->io->writeError('    Cleaning up', true, IOInterface::VERBOSE);
        $this->filesystem->unlink($packageArchive);
    }
}