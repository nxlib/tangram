<?php


namespace Tangram\Command;

use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tangram\Config;
use Tangram\Downloader\GitDownloader;
use Tangram\Framework\Framework;
use Tangram\IO\ConsoleIO;


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
        $fw = new Framework();
        $git = new GitDownloader(new ConsoleIO($input,$output,new HelperSet()),new Config());
        $git->doDownload($fw,"","https://github.com/nxlib/tangram-framework.git");
        $this->getIO()->write(<<<EOT
<comment>framwork done</comment>
EOT
        );
    }
}
