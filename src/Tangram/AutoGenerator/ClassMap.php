<?php
/**
 * Author: garming
 * Date: 26/03/2018
 * Time: 11:25
 */

namespace Tangram\AutoGenerator;


class ClassMap {
    const FILE_NAME = "autoload_classmap.php";

    /**
     * @var array
     */
    private $classMap;

    /**
     * @return array
     */
    public function getClassMap(): array
    {
        return $this->classMap;
    }

    /**
     * @param array $classMap
     */
    public function setClassMap(array $classMap)
    {
        $this->classMap = array_merge($this->classMap,$classMap);
    }


}