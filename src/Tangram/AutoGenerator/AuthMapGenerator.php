<?php
/**
 * Author: garming
 * Date: 26/03/2018
 * Time: 11:25
 */

namespace Tangram\AutoGenerator;


class AuthMapGenerator extends BaseGenerator
{
    public function __construct()
    {
        $this->fileName = "autoload_auth_map.php";
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
     * @return \Tangram\AutoGenerator\AuthMapGenerator
     */
    public function setClassMap(array $classMap):AuthMapGenerator
    {
        $this->classMap = array_merge($this->classMap,$classMap);
        return $this;
    }

    public function generate($absolutePathPerfix)
    {
        $content = [];
        foreach ($this->classMap as $k => $v) {
            $k = str_replace('\\',"\\\\",$k);
            if($v){
                $content[] = "        '{$k}' => true";
            }else{
                $content[] = "        '{$k}' => false";
            }
        }
        sort($content);
        $content = implode(",\r\n", $content);
        $this->write($absolutePathPerfix,$this->fileContent($content));
    }
    private function fileContent($str){
        return <<<"EOF"
<?php
class AutoLoadClassMap{
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