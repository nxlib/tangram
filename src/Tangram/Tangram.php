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

namespace Tangram;

use Tangram\Package\RootPackageInterface;
use Tangram\Package\Locker;
use Tangram\Repository\RepositoryManager;
use Tangram\Installer\InstallationManager;
use Tangram\Plugin\PluginManager;
use Tangram\Downloader\DownloadManager;
use Tangram\EventDispatcher\EventDispatcher;
use Tangram\Autoload\AutoloadGenerator;
use Tangram\Package\Archiver\ArchiveManager;

/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author Konstantin Kudryashiv <ever.zet@gmail.com>
 * @author Nils Adermann <naderman@naderman.de>
 */
class Tangram
{
    const VERSION = '@package_version@';
    const BRANCH_ALIAS_VERSION = '@package_branch_alias_version@';
    const RELEASE_DATE = '@release_date@';

    /**
     * @var Package\RootPackageInterface
     */
    private $package;

    /**
     * @var Locker
     */
    private $locker;

    /**
     * @var Repository\RepositoryManager
     */
    private $repositoryManager;

    /**
     * @var Downloader\DownloadManager
     */
    private $downloadManager;

    /**
     * @var Installer\InstallationManager
     */
    private $installationManager;

    /**
     * @var Plugin\PluginManager
     */
    private $pluginManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var Autoload\AutoloadGenerator
     */
    private $autoloadGenerator;

    /**
     * @var ArchiveManager
     */
    private $archiveManager;

    /**
     * @param  Package\RootPackageInterface $package
     * @return void
     */
    public function setPackage(RootPackageInterface $package)
    {
        $this->package = $package;
    }

    /**
     * @return Package\RootPackageInterface
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * @param Config $config
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param Package\Locker $locker
     */
    public function setLocker(Locker $locker)
    {
        $this->locker = $locker;
    }

    /**
     * @return Package\Locker
     */
    public function getLocker()
    {
        return $this->locker;
    }

    /**
     * @param Repository\RepositoryManager $manager
     */
    public function setRepositoryManager(RepositoryManager $manager)
    {
        $this->repositoryManager = $manager;
    }

    /**
     * @return Repository\RepositoryManager
     */
    public function getRepositoryManager()
    {
        return $this->repositoryManager;
    }

    /**
     * @param Downloader\DownloadManager $manager
     */
    public function setDownloadManager(DownloadManager $manager)
    {
        $this->downloadManager = $manager;
    }

    /**
     * @return Downloader\DownloadManager
     */
    public function getDownloadManager()
    {
        return $this->downloadManager;
    }

    /**
     * @param ArchiveManager $manager
     */
    public function setArchiveManager(ArchiveManager $manager)
    {
        $this->archiveManager = $manager;
    }

    /**
     * @return ArchiveManager
     */
    public function getArchiveManager()
    {
        return $this->archiveManager;
    }

    /**
     * @param Installer\InstallationManager $manager
     */
    public function setInstallationManager(InstallationManager $manager)
    {
        $this->installationManager = $manager;
    }

    /**
     * @return Installer\InstallationManager
     */
    public function getInstallationManager()
    {
        return $this->installationManager;
    }

    /**
     * @param Plugin\PluginManager $manager
     */
    public function setPluginManager(PluginManager $manager)
    {
        $this->pluginManager = $manager;
    }

    /**
     * @return Plugin\PluginManager
     */
    public function getPluginManager()
    {
        return $this->pluginManager;
    }

    /**
     * @param EventDispatcher $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return EventDispatcher
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * @param Autoload\AutoloadGenerator $autoloadGenerator
     */
    public function setAutoloadGenerator(AutoloadGenerator $autoloadGenerator)
    {
        $this->autoloadGenerator = $autoloadGenerator;
    }

    /**
     * @return Autoload\AutoloadGenerator
     */
    public function getAutoloadGenerator()
    {
        return $this->autoloadGenerator;
    }
}
