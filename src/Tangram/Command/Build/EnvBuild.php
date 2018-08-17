<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 11/01/2018
 * Time: 01:07
 */

namespace Tangram\Command\Build;


use Symfony\Component\Yaml\Yaml;
use Tangram\AutoGenerator\EnvMapGenerator;
use Tangram\Command\BaseCommandRun;
use Tangram\Resourse\Applications;

class EnvBuild extends BaseCommandRun
{
    public function exec($env = null){
        if(empty($env)){
            return;
        }
        /** @var \Tangram\Tangram $tangram */
        $tangram = $this->getTangram();

        $projectConfig = $tangram->getPorjectConfig();

        //check env config exist
        $file = empty($projectConfig->getEnvPath()) ? $projectConfig->getAbsoluteEnvPath().$env : $projectConfig->getAbsoluteEnvPath().DIRECTORY_SEPARATOR.$env;
        if(!file_exists($file)){
            $this->getIO()->writeError('<error>'.$projectConfig->getEnvPath().DIRECTORY_SEPARATOR.$env.' missing!!!</error>');
            exit;
        }
        $envConfig = Yaml::parse(file_get_contents($file));
        $applications = Applications::all();

        $this->writeHeader('🌋️ ENV Build: '.$env);

        foreach ($applications as $application) {
            $generator = new EnvMapGenerator();
            $this->writeHeader("    💡".$generator->getFileName()." >> $application");

            $generator->setConfig($envConfig)
                ->generate($projectConfig->getAbsoluteApplicationPath() . DIRECTORY_SEPARATOR . $application);

        }
    }
}