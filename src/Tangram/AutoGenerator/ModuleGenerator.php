<?php
/**
 * Author: garming
 * Date: 26/03/2018
 * Time: 11:25
 */

namespace Tangram\AutoGenerator;


use Symfony\Component\Filesystem\Filesystem;
use Tangram\Command\BaseCommand;

class ModuleGenerator extends BaseGenerator
{

    public function __construct(BaseCommand $command = null)
    {
        $this->fileName = "tangram.json";
        $this->fileData = [];
        $this->io = $command->getIO();
    }
    private $name;
    private $namespace;
    private $folder;
    /**
     * @var \Tangram\IO\IOInterface
     */
    private $io;
    /**
     * @var array
     */
    private $fileData;

    /**
     * @return array
     */
    public function getFileData(): array
    {
        return $this->fileData;
    }

    /**
     * @param $name
     * @param $namespace
     * @param $folder
     *
     * @return \Tangram\AutoGenerator\ModuleGenerator
     */
    public function setData($name,$namespace,$folder): ModuleGenerator
    {
        $this->name = ucfirst($name);
        $this->namespace = $namespace;
        $this->folder = $folder;
        return $this;
    }

    public function generate($absolutePathPerfix)
    {
        $file = new Filesystem();
        //mkdir module
        mkdir($absolutePathPerfix);

        //mkdir entity
        $this->io->write("<info>📁 mkdir folder: {$this->folder}/entity</info>");
        $folder = $absolutePathPerfix.DIRECTORY_SEPARATOR."entity";
        mkdir($folder);
        $this->io->write("<info>📝 touch file  : {$this->folder}/entity/{$this->name}.php</info>");
        $file->dumpFile($folder.DIRECTORY_SEPARATOR.$this->name.".php", $this->entityContent($this->name,$this->namespace));

        //mkdir exception
        $this->io->write("<info>📁 mkdir folder: {$this->folder}/exception</info>");
        mkdir($absolutePathPerfix.DIRECTORY_SEPARATOR."exception");

        //mkdir model
        $this->io->write("<info>📁 mkdir folder: {$this->folder}/model</info>");
        $folder = $absolutePathPerfix.DIRECTORY_SEPARATOR."model";
        mkdir($folder);
        $this->io->write("<info>📝 touch file  : {$this->folder}/model/{$this->name}Model.php</info>");
        $file->dumpFile($folder.DIRECTORY_SEPARATOR.$this->name."Model.php", $this->modelContent($this->name,$this->namespace));

        //mkdir service
        $this->io->write("<info>📁 mkdir folder: {$this->folder}/service</info>");
        $folder = $absolutePathPerfix.DIRECTORY_SEPARATOR."service";
        mkdir($folder);
        $this->io->write("<info>📝 touch file  : {$this->folder}/service/{$this->name}Service.php</info>");
        $file->dumpFile($folder.DIRECTORY_SEPARATOR.$this->name."Service.php", $this->serviceContent($this->name,$this->namespace));

        //create tangram.json
        $this->io->write("<info>📝 touch file  : {$this->fileName}</info>");
        $file->dumpFile($absolutePathPerfix.DIRECTORY_SEPARATOR.$this->fileName, $this->tangramJsonContent($this->name,$this->namespace));
    }

    private function entityContent($name,$namespace)
    {
        $date = date("Y-m-d H:i:s",time());
        $name = ucfirst($name);
        $namespace = trim($namespace,"\\");
        return <<<"EOF"
<?php
/**
 * User: Tangram Auto Creator
 * Date: {$date}
 */

namespace {$namespace}\Entity;

class {$name}
{
    //todo
}
EOF;
    }
    private function modelContent($name,$namespace)
    {
        $date = date("Y-m-d H:i:s",time());
        $name = ucfirst($name);
        $namespace = trim($namespace,"\\");
        return <<<"EOF"
<?php
/**
 * User: Tangram Auto Creator
 * Date: {$date}
 */

namespace {$namespace}\Model;

class {$name}Model
{
    //todo
}
EOF;
    }
    private function serviceContent($name,$namespace)
    {
        $date = date("Y-m-d H:i:s",time());
        $name = ucfirst($name);
        $namespace = trim($namespace,"\\");
        return <<<"EOF"
<?php
/**
 * User: Tangram Auto Creator
 * Date: {$date}
 */

namespace {$namespace}\Service;

class {$name}Service
{
    //todo
}
EOF;
    }
    private function tangramJsonContent($name,$namespace)
    {
        $namespace = ltrim($namespace,"\\");
        $namespace = str_replace("\\","\\\\",$namespace);
        return <<<"EOF"
{
  "name": "{$name}",
  "description": "",
  "keywords": [""],
  "type": "",
  "license": "",
  "authors": [
    {
      "name": "",
      "email": ""
    }
  ],
  "require": {
    "php": ">=7.0.0"
  },
  "require-module": {
  },
  "minimum-stability": "dev",
  "autoload": {
    "psr-4": {
      "{$namespace}\\\\": ""
    }
  }
}
EOF;
    }
}