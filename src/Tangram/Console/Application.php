<?php

/*
 * This file is part of Composer.
 *
 * (c) Nils Adermann <naderman@naderman.de>
 *     Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tangram\Console;

use Symfony\Component\Console\Output\ConsoleOutput;
use Tangram\Tangram;

class Application
{
    private static $logo = '   ________
  /__   __/__     ___   _____  _____ ___     ____ ___
    / / / __ `/  / __ \/ __  \/ ___/ __ `/  / __ `__ \ 
   / / / /__| \ / / / / /_ / / /  / /__| \ / / / / / / 
  /_/  \____\ \/_/ /_/\___/ /_/   \____\ \/_/ /_/ /_/ 
             `-`      __ / /            `-`
                     /____/   
';
    private static $line = '- - - - - - - - - - - - - - - - - - - - - - - - - - -';
    public function __construct()
    {
        //todo
    }
    public function run(){
        if(TG_COMMAND == ""){
            $this->info();
            echo $this->getHelp();
            exit;
        }
        //command
        if(TG_COMMAND != 'build'){
            exit("command \"".TG_COMMAND." \" not found!");
        }
        $tangramJsonFile = TG_RUN_PATH.DIRECTORY_SEPARATOR."tangram.json";
        if(!file_exists($tangramJsonFile)){
            exit("Error: tangram.js not found");
        }
        $tangramData = json_decode(file_get_contents($tangramJsonFile),1);
        $modulePath = 'modules';
        if(isset($tangramData['modules-path']) && !empty($tangramData['modules-path'])){
            $modulePath = $tangramData['modules-path'];
        }
        $modulePath = TG_RUN_PATH.DIRECTORY_SEPARATOR.$modulePath;
        if(!file_exists($modulePath)){
            exit("Error: module path not found");
        }

    }
    private function info(){
        console(self::$logo);
        $this->getVersion();
        $this->getLine();
    }
    private function getLine(){
        console(self::$line);
    }
    private function getVersion(){
        console("@version:".Tangram::VERSION);
    }
    private function getHelp(){
        return <<<'EOF'
command
    build [module] [--option]
    option:--router --permission
    eg: build tangram/demo --router
EOF;
    }

}
