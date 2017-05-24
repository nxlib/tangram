<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 24/05/2017
 * Time: 23:54
 */

namespace Tangram\Handler\Data;


use Tangram\Utils\Dir;

class Applications
{
    public static function scan($abs_path){
        $folder = Dir::scan($abs_path,1);
        if(!empty($folder)){
            return array_keys($folder);
        }
        return [];
    }
}