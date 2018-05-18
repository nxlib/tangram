<?php
/**
 * Author: garming
 * Date: 2018/4/8
 * Time: 23:53
 */

namespace Tangram\Application;


use Tangram\Config\ApplicationConfig;
use Tangram\Config\Exception\ApplicationConfigException;
use Tangram\IO\IOInterface;

class Application {
    /**
     * @var \Tangram\Config\ApplicationConfig
     */
    private $config;

    /**
     * @var string
     */
    private $absolutePath;

    /**
     * @var IOInterface
     */
    private $io;

    /**
     * @return \Tangram\Config\ApplicationConfig
     */
    public function getConfig(): \Tangram\Config\ApplicationConfig
    {
        return $this->config;
    }

    public function __construct(string $applicationName,string $absoluteApplicationPath,IOInterface $io)
    {
        try{
            $this->config = new ApplicationConfig($applicationName,$absoluteApplicationPath);
            $this->absolutePath = $absoluteApplicationPath.DIRECTORY_SEPARATOR.$applicationName;
            $this->io = $io;
        }catch (ApplicationConfigException $e) {
            $io->writeError($e->getMessage());
        }
    }

    /**
     * @return string
     */
    public function getAbsolutePath(): string
    {
        return $this->absolutePath;
    }

    /**
     * @return \Tangram\IO\IOInterface
     */
    public function getIo(): \Tangram\IO\IOInterface
    {
        return $this->io;
    }
}