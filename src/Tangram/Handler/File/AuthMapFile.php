<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 2017/3/13
 * Time: 20:22
 */

namespace Tangram\Handler\File;


class AuthMapFile
{
    const NAME = "autoload_auth_map.php";

    private function authMapFile($data){
        $str = implode(",\r\n",$data);
        return <<<"EOF"
<?php
class AutoAuthMap
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