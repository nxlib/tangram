<?php

/*
 * This file is part of Tangram.
 */

namespace Tangram;

use Tangram\Config;

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
