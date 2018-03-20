<?php


namespace Tangram\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class FrameworkCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('framework')
            ->setDescription('create demo framework')
            ->setHelp(<<<EOT
<info>create demo framework</info>
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //download url:https://github.com/nxlib/tangram-framework/archive/master.zip
        $this->getIO()->write(<<<EOT
<comment>framwork done</comment>
EOT
        );
    }
}
