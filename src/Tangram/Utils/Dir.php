<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 04/03/2017
 * Time: 13:05
 */

namespace Tangram\Utils;


class Dir
{
    public static function scan($path,$deep=1,$ignore = []){
        if($deep === 0){
            return [];
        }
        $_ignore = array_merge(['.','..','.DS_Store','.gitkeep','.svn'],$ignore);
        $dir = scandir($path);
        $rs = [];
        foreach ($dir as $key => $v){
            if(in_array($v,$_ignore)){
                continue;
            }
            if(is_dir($path.DIRECTORY_SEPARATOR.$v)){
                $rs[$v] = self::scan($path.DIRECTORY_SEPARATOR.$v,$deep-1);
            }else{
                $rs[] = $v;
            }
        }
        return $rs;
    }
    public static function create($path){
        if(!file_exists($path)){
            mkdir($path);
        }
    }
//    public static function scan4KeyValue($path,$deep=1,$ignore = []){
//        $data = self::scan($path,$deep,$ignore);
//        console($data);
//        $rs = [];
//        foreach ($data as $key => $v){
//            if(is_array($v)){
//                $rs[] = self::arrayHandler($key,$v);
//            }else{
//                $rs[] = $v;
//            }
//        }
//        return $rs;
//    }
//    private static function arrayHandler($key,$value){
//        if(is_array($value)){
//            $rs = $key.DIRECTORY_SEPARATOR;
//            if(empty($value)){
//                console($rs);
//                return $rs;
//            }else{
//                foreach ($value as $k => $v){
//                    if(is_array($v)){
//                        self::arrayHandler($rs.$k,$v);
//                    }else{
//                        return $rs.$v;
//                    }
//                }
//            }
//        }else{
//            $rs = DIRECTORY_SEPARATOR;
//
//            return $rs.$value;
//        }
//    }
}