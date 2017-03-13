<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 2017/3/13
 * Time: 20:21
 */

namespace Tangram\Handler\Data;


class TangramData
{
    const TANGRAM_FILE = TG_RUN_PATH.DIRECTORY_SEPARATOR."tangram.json";

    private static $data;

    public static function projectInfo(){
        if(!file_exists(TANGRAM_FILE)){
            exit("Error: {TANGRAM_FILE} not found");
        }
        $tangramData = json_decode(file_get_contents(TANGRAM_FILE),1);
        $modulePath = 'modules';


        if(isset($tangramData['modules-path']) && !empty($tangramData['modules-path'])){
            $modulePath = $tangramData['modules-path'];
        }
        $trueModulePath = TG_RUN_PATH.DIRECTORY_SEPARATOR.$modulePath;

        if(!file_exists($trueModulePath)){
            exit("Error: module path not found");
        }

        return $trueModulePath;
    }
    public static function getDefaultPath(){

    }
}