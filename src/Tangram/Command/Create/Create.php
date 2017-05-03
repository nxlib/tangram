<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 2017/5/3
 * Time: 14:11
 */

namespace Tangram\Command\Create;


use Tangram\Utils\Dir;

class Create
{
    public static function run($modulePath,$restfulPath,$webPagePath){
        console($modulePath);
        console($restfulPath);
        console($webPagePath);

        $module = explode("/",TG_MODULE);
        $module = end($module);
        $module = lcfirst($module);

        $modulePath = TG_RUN_PATH.DIRECTORY_SEPARATOR.$modulePath.DIRECTORY_SEPARATOR.$module;
        $restfulPath = TG_RUN_PATH.DIRECTORY_SEPARATOR.$restfulPath.DIRECTORY_SEPARATOR.$module;
        $webPagePath = TG_RUN_PATH.DIRECTORY_SEPARATOR.$webPagePath.DIRECTORY_SEPARATOR.$module;

        $all_dir = [
            $modulePath,
            $modulePath.DIRECTORY_SEPARATOR."entity",
            $modulePath.DIRECTORY_SEPARATOR."model",
            $modulePath.DIRECTORY_SEPARATOR."service",
            $modulePath.DIRECTORY_SEPARATOR."exception",
            $restfulPath,
            $restfulPath.DIRECTORY_SEPARATOR."controller",
            $webPagePath,
            $webPagePath.DIRECTORY_SEPARATOR."controller",
            $webPagePath.DIRECTORY_SEPARATOR."views",
        ];
        foreach ($all_dir as $v){
            Dir::create($v);
        }

    }
    private static function webPageTangramJsonFile($moduleName){
        $module = explode("/",$moduleName);
        $module = end($module);
        $upName = ucfirst($module);
        return <<<"EOF"
{
  "name": "{$moduleName}",
  "module":"{$module}",
  "description": "{$module}",
  "keywords": ["{$module}"],
  "type": "library",
  "license": "MIT",
  "authors": [
    {
      "name": "",
      "email": ""
    }
  ],
  "require": {
    "php": ">=7.0.0"
  },
  "require-module": {
  },
  "minimum-stability": "dev",
  "autoload": {
    "psr-4": {
      "{$upName}\\Controller\\": "controller"
    }
  }
}
EOF;
    }
    private static function restfulTangramJsonFile($moduleName){
        $module = explode("/",$moduleName);
        $module = end($module);
        $upName = ucfirst($module);
        return <<<"EOF"
{
  "name": "{$moduleName}",
  "module":"{$module}",
  "description": "{$module}",
  "keywords": ["{$module}"],
  "type": "library",
  "license": "MIT",
  "authors": [
    {
      "name": "",
      "email": ""
    }
  ],
  "require": {
    "php": ">=7.0.0"
  },
  "require-module": {
  },
  "minimum-stability": "dev",
  "autoload": {
    "psr-4": {
      "{$upName}\\RestController\\": "controller"
    }
  }
}
EOF;
    }
    private static function moduleTangramJsonFile($moduleName){
        $module = explode("/",$moduleName);
        $module = end($module);
        $upName = ucfirst($module);
        return <<<"EOF"
{
  "name": "{$moduleName}",
  "module":"{$module}",
  "description": "{$module}",
  "keywords": ["{$module}"],
  "type": "library",
  "license": "MIT",
  "authors": [
    {
      "name": "",
      "email": ""
    }
  ],
  "require": {
    "php": ">=7.0.0"
  },
  "require-module": {
  },
  "minimum-stability": "dev",
  "autoload": {
    "psr-4": {
      "{$upName}\\": ""
    }
  }
}
EOF;
    }
}