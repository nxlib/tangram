<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 2017/3/13
 * Time: 20:22
 */

namespace Tangram\Handler\File;


use Tangram\Utils\File;

class RealFile
{
    const NAME = "autoload_real.php";

    private static function file($md5)
    {
        return <<<"EOF"
<?php
include "autoload_router_map.php";
include "autoload_permission_map.php";
include "autoload_classmap.php";
include "autoload_auth_map.php";

class TangramAutoloaderInit{$md5} {
    public static function getLoader(){
        return;
    }
}
EOF;
    }

    public static function generate(string $md5)
    {
        $name = DefaultDir::AUTO_TANGRAM_FOLDER . DIRECTORY_SEPARATOR . self::NAME;
        File::create($name, self::file($md5));
    }
}