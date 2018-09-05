<?php


namespace Tangram\Command;

use function foo\func;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tangram\AutoGenerator\ModuleGenerator;


class CreateCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('create')
            ->setDefinition([new InputArgument("module",InputArgument::OPTIONAL,"create module")])
            ->setDescription('create module')
            ->setHelp(<<<EOT
<info>create module</info>
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tangram = $this->getTangram();
        $projectConfig = $tangram->getPorjectConfig();
        if(!is_writable($projectConfig->getAbsoluteModulePath())){
            $this->getIO()->write(<<<EOT
<error>module path isn't writable!!!</error>
<info>you can exec : chmod +r /your/module/path</info>
EOT
            );
            exit;
        }
        $name = $input->getArgument("module");
        $namespace = "";
        $folder = lcfirst($name);

        if(empty($name)){
            //ask info
            $name = $this->getIO()->askAndValidate("module: ",function($userInput){
                $pattern = "/^[a-zA-Z][0-9a-zA-Z_\/]+$/";
                if(preg_match($pattern,$userInput)){
                    return $userInput;
                }
                throw new \Exception("name is illegal! regular: {$pattern}");
            },3);
            $tmp = explode("/",$name);
            foreach ($tmp as $v){
                $namespace .= ucfirst($v)."\\";
            }
            if(count($tmp) > 1){
                $folder = lcfirst(end($tmp));
            }else{
                $folder = lcfirst($name);
            }
            $namespace = rtrim("\\".$namespace,"\\");
            $namespace = $this->getIO()->askAndValidate("namespace (default:{$namespace}): ",function($userInput){
                if(!empty($userInput)){
                    $pattern = "/^\\\\[a-zA-Z][0-9a-zA-Z_\\\\]+$/";
                    if(preg_match($pattern,$userInput)){
                        return $userInput;
                    }
                    throw new \Exception("namespace is illegal! regular: {$pattern}");
                }
            },3,$namespace);
        }else{
            //use default
            $namespace = "\\".ucfirst($name);
        }
        $generator = new ModuleGenerator($this);
        $generator->setData($name,$namespace,$folder)->generate($projectConfig->getAbsoluteModulePath().DIRECTORY_SEPARATOR.$folder);
    }
}
