<?php
/**
 * Author: garming
 * Date: 26/03/2018
 * Time: 17:14
 */

namespace Tangram\Command\Build;


use Symfony\Component\Filesystem\Filesystem;
use Tangram\AutoGenerator\AutoLoaderGenerator;
use Tangram\AutoGenerator\ClassMapGenerator;
use Tangram\Command\BaseCommandRun;
use Tangram\Module\Module;
use Tangram\Resourse\Applications;
use Tangram\Resourse\Modules;

class AutoLoaderBuild extends BaseCommandRun {


    public function exec($targetApplication = NULL)
    {
        /** @var \Tangram\Tangram $tangram */
        $tangram = $this->getTangram();
        $projectConfig = $tangram->getPorjectConfig();
        $applications = Applications::all();
        foreach ($applications as $application) {
            (new AutoLoaderGenerator())->generate($projectConfig->getAbsoluteApplicationPath() . DIRECTORY_SEPARATOR . $application);
        }
    }
}