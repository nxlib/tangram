<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 2017/3/13
 * Time: 20:22
 */

namespace Tangram\AutoFile;


use Tangram\Utils\File;

class RouterMapFile
{
    const NAME = "autoload_router_map.php";

    private static function file($data)
    {
        asort($data);
        $str = implode(",\r\n", $data);
        return <<<"EOF"
<?php
class AutoRouterMap
{
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

    public static function generate($data)
    {
        $name = DefaultDir::autoTangramSavePath() . DIRECTORY_SEPARATOR . self::NAME;
        $routerMapFileData = [];
        foreach ($data as $key => $value) {
            $tmp = "";
            foreach ($value as $k => $v) {
                $tmp .= "'{$k}' => '{$v}', ";
            }
            $tmp = rtrim($tmp, ' ,');
            $routerMapFileData[] = "        '{$key}' => [{$tmp}]";
        }
        File::create($name, self::file($routerMapFileData));
    }
}