<?php
/**
 * Author: garming
 * Date: 26/03/2018
 * Time: 11:25
 */

namespace Tangram\AutoGenerator;


class ViewMapGenerator extends BaseGenerator
{
    public function __construct()
    {
        $this->fileName = "autoload_view_map.php";
        $this->classMap = [];
    }

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
     *
     * @return \Tangram\AutoGenerator\ViewMapGenerator
     */
    public function setClassMap(array $classMap):ViewMapGenerator
    {
        $this->classMap = array_merge($this->classMap,$classMap);
        return $this;
    }

    public function generate($absolutePathPerfix)
    {
        $content = [];
        foreach ($this->classMap as $k => $v) {
            $k = str_replace('\\',"\\\\",$k);
            $content[] = "        '{$k}' => '{$v}'";
        }
        sort($content);
        $content = implode(",\r\n", $content);
        $this->write($absolutePathPerfix,$this->fileContent($content));
    }
    private function fileContent($str){
        return <<<"EOF"
<?php
class AutoViewPath{
    private static \$map = [
{$str}
    ];
    public static function getMap()
    {
        return static::\$map;
    }
}
EOF;
    }
}