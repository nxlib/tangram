<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 2017/3/13
 * Time: 20:22
 */

namespace Tangram\Handler\File;


use Tangram\Utils\File;

class AuthMapFile
{
    const NAME = "autoload_auth_map.php";

    private static function file($data)
    {
        $str = implode(",\r\n", $data);
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

    /**
     * @param $data
     */
    public static function generate($data)
    {
        $name = DefaultDir::autoTangramSavePath() . DIRECTORY_SEPARATOR . self::NAME;
        $authMapFileData = [];
        foreach ($data as $key => $value) {
            if ($value) {
                $authMapFileData[] = "        '{$key}' => true";
            } else {
                $authMapFileData[] = "        '{$key}' => false";
            }

        }
        File::create($name, self::file($authMapFileData));
    }
}