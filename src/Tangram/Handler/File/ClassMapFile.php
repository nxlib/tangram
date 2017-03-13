<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 2017/3/13
 * Time: 20:22
 */

namespace Tangram\Handler\File;


class ClassMapFile
{
    const NAME = "autoload_classmap.php";

    private function classMapFile($data){
        $str = implode(",\r\n",$data);
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