<?php
/**
 * Author: garming
 * Date: 26/03/2018
 * Time: 17:14
 */

namespace Tangram\Command\Build;


use Tangram\Command\BaseCommandRun;
use Tangram\Module\Module;
use Tangram\Resourse\Applications;
use Tangram\Resourse\Modules;

class ClassMapBuild extends BaseCommandRun
{
    public function exec($targetApplication = null){
//        $tangram = $this->getTangram();
//        $projectConfig = $tangram->getPorjectConfig();
//
//        $modules = Modules::all();
//        foreach ($modules as $module){
//            $module = new Module($module,$projectConfig->getAbsoluteModulePath(),$this->getIO());
//        }
//        $applications = Applications::all();
//        foreach ($applications as $application){
//            $application = new Module($application,$projectConfig->getAbsoluteApplicationPath(),$this->getIO());
//        }
    }
}