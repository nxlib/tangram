<?php
/**
 * Author: garming
 * Date: 30/03/2018
 * Time: 00:36
 */

namespace Tangram\AutoGenerator;


use Symfony\Component\Filesystem\Filesystem;

class BaseGenerator {
    private $module = 'tangram-modules';
    private $autoFolder = 'auto-tangram';
    protected $fileName;

    protected function write($absolutePathPerfix,$content)
    {
        $file = new Filesystem();
        $name = $absolutePathPerfix.
            DIRECTORY_SEPARATOR.
            $this->module.
            DIRECTORY_SEPARATOR.
            $this->autoFolder.
            DIRECTORY_SEPARATOR.
            $this->fileName;
        $file->dumpFile($name,$content);
    }
    protected function writeAutoLoad($absolutePathPerfix,$content)
    {
        $file = new Filesystem();
        $name = $absolutePathPerfix.
            DIRECTORY_SEPARATOR.
            $this->module.
            DIRECTORY_SEPARATOR.
            "autoload.php";
        $file->dumpFile($name,$content);
    }

    /**
     * @return mixed
     */
    public function getFileName()
    {
        return $this->fileName;
    }
}