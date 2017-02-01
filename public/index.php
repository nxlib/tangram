<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 2016/12/19
 * Time: 16:40
 */
function pr($data = null){
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}
$path = dirname(dirname(__FILE__));

//composer
include $path.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

//tangram
include $path.DIRECTORY_SEPARATOR.'tangram-modules'.DIRECTORY_SEPARATOR.'autoload.php';

pr(AutoPermissionMap::getMap());
pr(AutoRouterMap::getMap());