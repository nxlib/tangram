<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 2017/3/13
 * Time: 20:22
 */

namespace Tangram\Handler\File;


class PermissionMapFile
{
    const NAME = "autoload_permission_map.php";

    private function permissionMapFile($data){
        $str = implode(",\r\n",$data);
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
}