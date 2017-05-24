<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 2017/3/13
 * Time: 20:22
 */

namespace Tangram\Handler\File;


use Tangram\Utils\File;

class ViewsPathFile
{
    const NAME = "autoload_views_path.php";

    private static function file($data)
    {
//        $name = DefaultDir::AUTO_TANGRAM_FOLDER.DIRECTORY_SEPARATOR.NAME;
        $str = implode(",\r\n", $data);
        return <<<"EOF"
<?php
class AutoViewsPath{
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
        $classMap = [];
        foreach ($data as $k => $v) {
            $classMap[] = "        '{$k}' => '{$v}'";
        }
        File::create($name, self::file($classMap));
    }
}