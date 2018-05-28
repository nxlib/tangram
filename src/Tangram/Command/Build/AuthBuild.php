<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 11/01/2018
 * Time: 01:07
 */

namespace Tangram\Command\Build;


use Tangram\Application\Application;
use Tangram\AutoGenerator\AuthMapGenerator;
use Tangram\Command\BaseCommandRun;
use Tangram\Reflection\AnnotationReflection;
use Tangram\Resourse\Applications;

class AuthBuild extends BaseCommandRun
{

    public function exec($targetApplication = null)
    {
        $this->writeHeader('🚥 Build Auth ');
        $tangram = $this->getTangram();
        $projectConfig = $tangram->getPorjectConfig();

        $applications = Applications::all();
        foreach ($applications as $application) {
            $applicationInstance = new Application(
                $application,
                $projectConfig->getAbsoluteApplicationPath(),
                $this->getIO()
            );
            $authMap = AnnotationReflection::getAnnotations($applicationInstance,"auth");
            $generator = new AuthMapGenerator();
            $this->writeHeader("    💡".$generator->getFileName()."       >> $application");
            $generator->setClassMap($authMap)
                ->generate($projectConfig->getAbsoluteApplicationPath() . DIRECTORY_SEPARATOR . $application);
        }
    }
}