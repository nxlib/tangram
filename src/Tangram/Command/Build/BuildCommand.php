<?php


namespace Tangram\Command\Build;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tangram\Command\BaseCommand;


class BuildCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('build')
            ->setDefinition($this->setDefinitions())
            ->setDescription('build module')
            ->setHelp(<<<EOT
<info>build module</info>
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $env = $input->getArgument("env");

        if(array_sum($input->getOptions()) === 0){
            $input->setOption('router',1);
            $input->setOption('permission',1);
            $input->setOption('auth',1);
            $input->setOption('view',1);
        }

        foreach ($input->getOptions() as $k => $v){
            if($v){
                switch ($k){
                    case 'router':
                        (new RouterBuild($this))->exec();
                        break;
                    case 'auth':
                        (new AuthBuild($this))->exec();
                        break;
                    case 'permission':
                        (new PermissionBuild($this))->exec();
                        break;
                    case 'view':
                        (new ViewBuild($this))->exec();
                        break;
                    default:
                        break;
                }
            }
        }
        (new EnvBuild($this))->exec($env);
        //command
        $this->writeHeader("");
        (new ClassMapBuild($this))->exec();
        (new AutoLoaderBuild($this))->exec();
    }
    private function setDefinitions():array
    {
        return [
            new InputOption('router', 'r', InputOption::VALUE_NONE, 'only build router'),
            new InputOption('permission', 'p', InputOption::VALUE_NONE, 'only build premission'),
            new InputOption('auth', 'a', InputOption::VALUE_NONE, 'only build auth'),
            new InputOption('view', 'w', InputOption::VALUE_NONE, 'only build view'),
            new InputArgument("env",InputArgument::OPTIONAL,"build env")
        ];
    }
    protected function writeHeader($title)
    {
        $this->getIO()->write(<<<EOT
<info>$title</info>
EOT
        );
    }
}
