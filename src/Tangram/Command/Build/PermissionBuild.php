<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 11/01/2018
 * Time: 01:07
 */

namespace Tangram\Command\Build;


use Tangram\Application\Application;
use Tangram\AutoGenerator\PermissionMapGenerator;
use Tangram\Command\BaseCommandRun;
use Tangram\Reflection\AnnotationReflection;
use Tangram\Resourse\Applications;

class PermissionBuild extends BaseCommandRun
{
    public function exec($targetApplication = null){
        $this->writeHeader('👮Build Permission');
        //permission可以放在controller或者action上，这里的实现只解析permission注解，业务逻辑不实现
        /** @var \Tangram\Tangram $tangram */
        $tangram = $this->getTangram();

        $projectConfig = $tangram->getPorjectConfig();

        $applications = Applications::all();
        foreach ($applications as $application) {
            $applicationInstance = new Application(
                $application,
                $projectConfig->getAbsoluteApplicationPath(),
                $this->getIO()
            );
            $routerMap = AnnotationReflection::getAnnotations($applicationInstance,"permission");
            $generator = new PermissionMapGenerator();
            $this->writeHeader("    💡".$generator->getFileName()." >> $application");

            $generator->setClassMap($routerMap)
                ->generate($projectConfig->getAbsoluteApplicationPath() . DIRECTORY_SEPARATOR . $application);

        }
    }
}