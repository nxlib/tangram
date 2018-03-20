<?php

function includeIfExists($file)
{
  return file_exists($file) ? include $file : false;
}

if ((!$loader = includeIfExists(__DIR__.'/../vendor/autoload.php')) && (!$loader = includeIfExists(__DIR__.'/../../../autoload.php'))) {
  echo 'You must set up the module dependencies using `tangram install`'.PHP_EOL.
    'See https://tangram.nxlib.xyz for instructions on installing Tangram'.PHP_EOL;
  exit(1);
}

return $loader;
