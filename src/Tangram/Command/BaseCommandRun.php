<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 11/01/2018
 * Time: 02:00
 */

namespace Tangram\Command;


use Tangram\IO\IOInterface;

class BaseCommandRun
{
    /**
     * @var IOInterface
     */
    private $io;

    public function __construct(IOInterface $io)
    {
        $this->io = $io;
    }
    public function getIO(){
        return $this->io;
    }
    public function writeHeader($title)
    {
        $this->io->write(<<<EOT
<info>$title</info>
EOT
        );
    }
}