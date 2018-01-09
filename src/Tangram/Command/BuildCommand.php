<?php


namespace Tangram\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class BuildCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('build')
            ->setDescription('build module')
            ->setHelp(<<<EOT
<info>build module</info>
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getIO()->write(<<<EOT
<info>Tangram - Module Management for PHP</info>
<comment>Tangram is a dependency manager tracking local dependencies of your projects and libraries.
See https://nxlib.xyz/ for more information.</comment>
EOT
        );
    }
}
