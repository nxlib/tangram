<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 2017/2/4
 * Time: 15:21
 */

namespace NxLib\Permission\Inspector;


use NxLib\Permission\Exception\PermissionException;

class Inspector
{
    public function check(){
        $map = \AutoPermissionMap::getMap();
        if(!empty($map)){
            pr($map);
        }
    }
}