<?php
/**
 * Author: garming
 * Date: 26/03/2018
 * Time: 11:25
 */

namespace Tangram\AutoGenerator;


class RouterMapGenerator extends BaseGenerator
{
    public function __construct()
    {
        $this->fileName = "autoload_router_map.php";
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
     * @return \Tangram\AutoGenerator\RouterMapGenerator
     */
    public function setClassMap(array $classMap):RouterMapGenerator
    {
        $this->classMap = array_merge($this->classMap,$classMap);
        return $this;
    }

    public function generate($absolutePathPerfix)
    {
        $content = [];
        foreach ($this->classMap as $k => $v) {
            $key = strtoupper($v["method"])."#".$v["uri"];
            $value = "'namespace' => '{$v["namespace"]}',";
            $value .= "'class' => '{$v["class"]}',";
            $value .= "'function' => '{$v["function"]}'";
            $content[] = "        '{$key}' => [{$value}]";
        }
        sort($content);
        $content = implode(",\r\n", $content);
        $this->write($absolutePathPerfix,$this->fileContent($content));
    }
    private function fileContent($str){
        return <<<"EOF"
<?php
class AutoRouterMap{
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