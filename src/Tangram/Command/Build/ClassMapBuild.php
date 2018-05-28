<?php
/**
 * Author: garming
 * Date: 26/03/2018
 * Time: 17:14
 */

namespace Tangram\Command\Build;


use Tangram\Application\Application;
use Tangram\AutoGenerator\ClassMapGenerator;
use Tangram\Command\BaseCommandRun;
use Tangram\Module\Module;
use Tangram\Resourse\Applications;
use Tangram\Resourse\Modules;

class ClassMapBuild extends BaseCommandRun {

    private $classMap = [];

    public function exec($targetApplication = NULL)
    {
        /** @var \Tangram\Tangram $tangram */
        $tangram = $this->getTangram();

        $projectConfig = $tangram->getPorjectConfig();

        $modules = Modules::all();
        foreach ($modules as $module) {
            $module = new Module(
                $module,
                $projectConfig->getAbsoluteModulePath(),
                $this->getIO()
            );
            $this->classMap = array_merge(
                $this->classMap,
                $module->getConfig()->getPsr4Autoload()
            );
        }
        $applications = Applications::all();
        foreach ($applications as $application) {
            $applicationInstance = new Application(
                $application,
                $projectConfig->getAbsoluteApplicationPath(),
                $this->getIO()
            );
            $this->classMap = array_merge(
                $this->classMap,
                $applicationInstance->getConfig()->getPsr4Autoload()
            );
            $this->writeHeader("💫 autoload_classmap.php          >> {$application} ");
            (new ClassMapGenerator())->setClassMap($this->classMap)
                ->generate($projectConfig->getAbsoluteApplicationPath() . DIRECTORY_SEPARATOR . $application);

        }
    }

    /**
     * @return mixed
     */
    public function getClassMap()
    {
        return $this->classMap;
    }
}