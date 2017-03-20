<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 2017/3/20
 * Time: 10:27
 */

namespace Tangram\Handler\Data;


class PathData
{
    private $path;
    private $absolutePath;
    public function __construct($path)
    {
        $this->path = $path;
        $this->absolutePath = TG_RUN_PATH.DIRECTORY_SEPARATOR.$path;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getAbsolutePath(): string
    {
        return $this->absolutePath;
    }
}