<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 2017/3/13
 * Time: 20:22
 */

namespace Tangram\Handler\File;


class RealFile
{
    const NAME = "autoload_real.php";

    private function realFile($md5){
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
}