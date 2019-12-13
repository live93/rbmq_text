<?php
namespace app\index\controller;

use think\Controller;
use think\Db;

class User extends Controller
{
    public function index()
    {
        $usersData = Db::name('user')->where('status',1)->select();
        $this->assign('usersData',$usersData);
        return $this->fetch();
    }
}
