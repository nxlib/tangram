<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 03/02/2017
 * Time: 19:03
 */

namespace NxLib\User\Controller;


use NxLib\User\Model\UserModel;

class RegisterController
{
    public function index(){
        $user = (new UserModel())->getAll();
        pr($user);
    }
}