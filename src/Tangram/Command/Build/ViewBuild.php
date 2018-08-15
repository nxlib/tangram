<?php
/**
 * Author: garming
 * Date: 26/03/2018
 * Time: 17:14
 */

namespace Tangram\Command\Build;


use Tangram\Application\Application;
use Tangram\AutoGenerator\ViewMapGenerator;
use Tangram\Command\BaseCommandRun;
use Tangram\Resourse\Applications;

class ViewBuild extends BaseCommandRun
{

    public function exec($targetApplication = NULL)
    {
        $this->writeHeader('📄 Build Views ');
        /** @var \Tangram\Tangram $tangram */
        $tangram = $this->getTangram();

        $projectConfig = $tangram->getPorjectConfig();

        $applications = Applications::all();
        foreach ($applications as $applicationName) {
            $applicationInstance = new Application(
                $applicationName,
                $projectConfig->getAbsoluteApplicationPath(),
                $this->getIO()
            );
            $appConfig = $applicationInstance->getConfig();
            $viewMap = [];
            if (!empty($appConfig->getView())) {
                foreach ($appConfig->getPsr4Autoload() as $namespace => $path) {
                    $pwd = dirname(
                            $projectConfig->getAbsoluteApplicationPath() .
                            DIRECTORY_SEPARATOR .
                            $applicationName .
                            DIRECTORY_SEPARATOR .
                            $path) . DIRECTORY_SEPARATOR . $appConfig->getView();
                    if (file_exists($pwd)) {
                        $viewMap[$namespace] = dirname(
                                $projectConfig->getApplicationPath() .
                                DIRECTORY_SEPARATOR .
                                $applicationName .
                                DIRECTORY_SEPARATOR .
                                $path) . DIRECTORY_SEPARATOR . $appConfig->getView();
                    }
                }
            }
            $generator = new ViewMapGenerator();
            $this->writeHeader("    💡" . $generator->getFileName() . "     >> $applicationName");

            $generator->setClassMap($viewMap)
                ->generate($projectConfig->getAbsoluteApplicationPath() . DIRECTORY_SEPARATOR . $applicationName);

        }
    }
}