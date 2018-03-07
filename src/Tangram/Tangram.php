<?php

/*
 * This file is part of Tangram.
 */

namespace Tangram;

use Tangram\Config;
use Tangram\Downloader\DownloadManager;
use Tangram\Autoload\AutoloadGenerator;

class Tangram
{
    const VERSION = '@package_version@';
    const BRANCH_ALIAS_VERSION = '@package_branch_alias_version@';
    const RELEASE_DATE = '@release_date@';

    /**
     * @var \Tangram\Config
     */
    private $config;
    /**
     * @var Downloader\DownloadManager
     */
    private $downloadManager;


    /**
     * @var Autoload\AutoloadGenerator
     */
    private $autoloadGenerator;


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

    /**
     * @return \Tangram\Config
     */
    public function getConfig(): Config {
        return $this->config;
    }

    /**
     * @param \Tangram\Config $config
     */
    public function setConfig(Config $config) {
        $this->config = $config;
    }
}
