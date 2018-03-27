<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 2017/3/13
 * Time: 20:22
 */

namespace Tangram\AutoFile;


use Tangram\Utils\File;

class RealFile
{
    const NAME = "autoload_real.php";

    private static function file($md5)
    {
        return <<<"EOF"
<?php
include __DIR__.DIRECTORY_SEPARATOR."autoload_router_map.php";
include __DIR__.DIRECTORY_SEPARATOR."autoload_permission_map.php";
include __DIR__.DIRECTORY_SEPARATOR."autoload_classmap.php";
include __DIR__.DIRECTORY_SEPARATOR."autoload_auth_map.php";
include __DIR__.DIRECTORY_SEPARATOR."autoload_views_path.php";

class TangramAutoloaderInit{$md5} {
    public static function getLoader(){
        return;
    }
}
EOF;
    }

    public static function generate(string $md5)
    {
        $name = DefaultDir::autoTangramSavePath() . DIRECTORY_SEPARATOR . self::NAME;
        File::create($name, self::file($md5));
    }
}