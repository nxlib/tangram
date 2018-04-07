<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 11/01/2018
 * Time: 02:00
 */

namespace Tangram\Command;

class BaseCommandRun
{

    /**
     * @var \Tangram\Command\BaseCommand
     */
    private $instance;

    public function __construct(BaseCommand $instance)
    {
        $this->instance = $instance;
    }

    public function writeHeader($title)
    {
        $this->getIO()->write(<<<EOT
<info>$title</info>
EOT
        );
    }

    /**
     * @return \Tangram\IO\IOInterface
     */
    public function getIO(): \Tangram\IO\IOInterface
    {
        return $this->instance->getIO();
    }
    public function __call($name, $arguments)
    {
        return $this->instance->$name($arguments);
    }
}