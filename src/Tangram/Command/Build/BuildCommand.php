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
        pr($input);
        $app = $input->getArgument("application");
        if(array_sum($input->getOptions()) === 0){
            $input->setOption('router',1);
            $input->setOption('premission',1);
            $input->setOption('auth',1);
        }
        foreach ($input->getOptions() as $k => $v){
            if($v){
                switch ($k){
                    case 'router':
                        (new RouterBuild($this->getIO()))->exec();
                        break;
                    case 'premission':
                        (new PremissionBuild($this->getIO()))->exec();
                        break;
                    case 'auth':
                        (new AuthBuild($this->getIO()))->exec();
                        break;
                    default:
                        break;
                }
            }
        }
    }
    private function setDefinitions():array
    {
        return [
            new InputOption('router', 'r', InputOption::VALUE_NONE, 'only build router'),
            new InputOption('premission', 'p', InputOption::VALUE_NONE, 'only build premission'),
            new InputOption('auth', 'a', InputOption::VALUE_NONE, 'only build auth'),
            new InputArgument("application",InputArgument::OPTIONAL,"build for the target application,default all applications")
        ];
    }
}
