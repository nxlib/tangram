<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 04/03/2017
 * Time: 18:52
 */

namespace Tangram\Utils;


class File
{
    public static function create($name,string $data){
        $fp=fopen($name,"w");
        fwrite($fp,$data);
        fclose($fp);
    }
}