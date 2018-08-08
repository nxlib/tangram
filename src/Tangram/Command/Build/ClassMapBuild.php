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
        foreach ($modules as $moduleName) {
            $module = new Module(
              $moduleName,
                $projectConfig->getAbsoluteModulePath(),
                $this->getIO()
            );
            $psr4 = $module->getConfig()->getPsr4Autoload();
            foreach ($psr4 as &$value){
              $value = rtrim($projectConfig->getModulePath().DIRECTORY_SEPARATOR.$moduleName.DIRECTORY_SEPARATOR.$value,DIRECTORY_SEPARATOR);
            }
            $this->classMap = array_merge(
                $this->classMap,
                $psr4
            );
        }
        $applications = Applications::all();
        foreach ($applications as $applicationName) {
            $applicationInstance = new Application(
                $applicationName,
                $projectConfig->getAbsoluteApplicationPath(),
                $this->getIO()
            );
            $psr4 = $applicationInstance->getConfig()->getPsr4Autoload();
            foreach ($psr4 as &$value){
                $value = rtrim($projectConfig->getApplicationPath().DIRECTORY_SEPARATOR.$applicationName.DIRECTORY_SEPARATOR.$value,DIRECTORY_SEPARATOR);
            }
            $this->classMap = array_merge(
                $this->classMap,
                $psr4
            );
            $this->writeHeader("💫 autoload_classmap.php          >> {$applicationName} ");
            (new ClassMapGenerator())->setClassMap($this->classMap)
                ->generate($projectConfig->getAbsoluteApplicationPath() . DIRECTORY_SEPARATOR . $applicationName);

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