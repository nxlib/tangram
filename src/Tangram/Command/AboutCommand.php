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

namespace Tangram\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class AboutCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('about')
            ->setDescription('Shows the short information about Tangram.')
            ->setHelp(<<<EOT
<info>php tangram.phar about</info>
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
