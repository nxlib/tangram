<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 2017/5/3
 * Time: 14:11
 */

namespace Tangram\Command\Create;


use Tangram\Utils\Dir;
use Tangram\Utils\File;

class Create
{
    private static $moduleName;
    private static $module;
    private static $upModule;
    private static $lowModule;

    public static function run($modulePath, $restfulPath, $webPagePath)
    {
        static::$moduleName = TG_MODULE;
        $module = explode("/", TG_MODULE);
        static::$module = end($module);
        static::$upModule = ucfirst(static::$module);
        static::$lowModule = lcfirst(static::$module);

        $modulePath = TG_RUN_PATH . DIRECTORY_SEPARATOR . $modulePath . DIRECTORY_SEPARATOR . static::$module;
        $restfulPath = TG_RUN_PATH . DIRECTORY_SEPARATOR . $restfulPath . DIRECTORY_SEPARATOR . static::$module;
        $webPagePath = TG_RUN_PATH . DIRECTORY_SEPARATOR . $webPagePath . DIRECTORY_SEPARATOR . static::$module;

        $all_dir = [
            $modulePath,
            $modulePath . DIRECTORY_SEPARATOR . "entity",
            $modulePath . DIRECTORY_SEPARATOR . "model",
            $modulePath . DIRECTORY_SEPARATOR . "service",
            $modulePath . DIRECTORY_SEPARATOR . "exception",
            $restfulPath,
            $restfulPath . DIRECTORY_SEPARATOR . "controller",
            $webPagePath,
            $webPagePath . DIRECTORY_SEPARATOR . "controller",
            $webPagePath . DIRECTORY_SEPARATOR . "views",
        ];
        foreach ($all_dir as $v) {
            Dir::create($v);
        }
        if(!file_exists($modulePath . DIRECTORY_SEPARATOR . "tangram.json")){
            File::create($modulePath . DIRECTORY_SEPARATOR . "tangram.json", self::moduleTangramJsonFile());
            File::create($modulePath . DIRECTORY_SEPARATOR . "entity" . DIRECTORY_SEPARATOR . static::$upModule . ".php", self::entityFile());
            File::create($modulePath . DIRECTORY_SEPARATOR . "model" . DIRECTORY_SEPARATOR . static::$upModule . "Model.php", self::modelFile());
            File::create($modulePath . DIRECTORY_SEPARATOR . "service" . DIRECTORY_SEPARATOR . static::$upModule . "Service.php", self::serviceFile());
        }else{
            console("module is exist");
        }
        if(!file_exists($restfulPath . DIRECTORY_SEPARATOR . "tangram.json")){
            File::create($restfulPath . DIRECTORY_SEPARATOR . "tangram.json", self::restfulTangramJsonFile());
            File::create($restfulPath . DIRECTORY_SEPARATOR . "controller" . DIRECTORY_SEPARATOR . static::$upModule . "Controller.php", self::restFile());
        }else{
            console("restful is exist");
        }
        if(!file_exists($webPagePath . DIRECTORY_SEPARATOR . "tangram.json")){
            File::create($webPagePath . DIRECTORY_SEPARATOR . "tangram.json", self::webPageTangramJsonFile());
            File::create($webPagePath . DIRECTORY_SEPARATOR . "controller" . DIRECTORY_SEPARATOR . static::$upModule . "Controller.php", self::controllerFile());
        }else{
            console("web-page is exist");
        }




    }

    private static function webPageTangramJsonFile()
    {
        $moduleName = static::$moduleName;
        $module = static::$module;
        $upName = static::$upModule;
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
      "{$upName}\\\\Controller\\\\": "controller"
    }
  }
}
EOF;
    }

    private static function restfulTangramJsonFile()
    {
        $moduleName = static::$moduleName;
        $module = static::$module;
        $upName = static::$upModule;
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
      "{$upName}\\\\RestController\\\\": "controller"
    }
  }
}
EOF;
    }

    private static function moduleTangramJsonFile()
    {
        $moduleName = static::$moduleName;
        $module = static::$module;
        $upName = static::$upModule;
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
      "{$upName}\\\\": ""
    }
  }
}
EOF;
    }

    private static function entityFile()
    {
        $upName = static::$upModule;
        $date = date("Y-m-d H:i:s", time());
        return <<<"EOF"
<?php
/**
 * User: Tangram Auto Creator
 * Date: {$date}
 */

namespace {$upName}\Entity;


class {$upName}
{
    
}
EOF;
    }

    private static function modelFile()
    {
        $upName = static::$upModule;
        $date = date("Y-m-d H:i:s", time());
        return <<<"EOF"
<?php
/**
 * User: Tangram Auto Creator
 * Date: {$date}
 */

namespace {$upName}\Model;


class {$upName}Model
{
    
}
EOF;
    }

    private static function serviceFile()
    {
        $upName = static::$upModule;
        $date = date("Y-m-d H:i:s", time());
        return <<<"EOF"
<?php
/**
 * User: Tangram Auto Creator
 * Date: {$date}
 */

namespace {$upName}\Service;


class {$upName}Service
{
    
}
EOF;
    }

    private static function restFile()
    {
        $upName = static::$upModule;
        $date = date("Y-m-d H:i:s", time());
        return <<<"EOF"
<?php
/**
 * User: Tangram Auto Creator
 * Date: {$date}
 */

namespace {$upName}\RestController;


class {$upName}Controller
{
    
}
EOF;
    }

    private static function controllerFile()
    {
        $upName = static::$upModule;
        $date = date("Y-m-d H:i:s", time());
        return <<<"EOF"
<?php
/**
 * User: Tangram Auto Creator
 * Date: {$date}
 */

namespace {$upName}\Controller;


class {$upName}Controller
{
    
}
EOF;
    }
}