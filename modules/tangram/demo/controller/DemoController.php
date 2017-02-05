<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 05/02/2017
 * Time: 13:57
 */

namespace Tangram\Demo\Controller;


use Common\TGController;

class DemoController extends TGController
{
    public function getDemo()
    {
        pr($this->getParam('name'));
    }
    public function postDemo()
    {
        pr($this->postParam('name'));
    }
    public function putDemo()
    {
        pr($this->putParam('name'));
    }
    public function deleteDemo()
    {
        pr($this->deleteParam('name'));
    }
}