<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 2017/3/13
 * Time: 20:22
 */

namespace Tangram\Handler\File;


use Tangram\Utils\File;

class PermissionMapFile
{
    const NAME = "autoload_permission_map.php";

    private static function file($data)
    {
        $str = implode(",\r\n", $data);
        return <<<"EOF"
<?php
class AutoPermissionMap
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
        $permissionMapFileData = [];
        foreach ($data as $key => $value) {
            $tmp = "";
            foreach ($value as $k => $v) {
                if ($k == 'rest') {
                    if ($v) {
                        $tmp .= "'{$k}' => true, ";
                    } else {
                        $tmp .= "'{$k}' => false, ";
                    }

                } else {
                    $tmp .= "'{$k}' => '{$v}', ";
                }
            }
            $tmp = rtrim($tmp, ' ,');
            $permissionMapFileData[] = "        '{$key}' => [{$tmp}]";
        }
        File::create($name, self::file($permissionMapFileData));
    }
}