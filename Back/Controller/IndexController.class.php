<?php
namespace Back\Controller;
use Common\Tools\BackController;
//use Think\Controller;

class IndexController extends BackController {
    function __construct(){
        parent::__construct();//先执行父类构造方法
        layout(false); // 临时关闭当前模板的布局功能
    }

    public function head(){
        $this -> display();
    }
    public function left(){
        //通过"管理员"获得对应的"角色"，通过角色获得对应"权限"并显示
        $admin_id = session('admin_id');
        $admin_name = session('admin_name');
        if($admin_name !== 'admin'){//普通用户获取权限
            $auth_ids = D('Manager')->alias('m')->join('__ROLE__ r on m.mg_role_id=r.role_id')->field('r.role_auth_ids')->where(array('m.mg_id'=>$admin_id))->find();
            //SELECT r.role_auth_ids FROM php41_manager m INNER JOIN php41_role r on m.mg_role_id=r.role_id WHERE m.mg_id = '2'
            $auth_ids = $auth_ids['role_auth_ids'];
            //dump($auth_ids);//string(23) "101,105,106,102,107,108"

            //根据auth_ids获得对应的权限信息
            $auth_infoA = D('Auth')->where("auth_id in ($auth_ids) and auth_level=0")->select();//顶级权限
            $auth_infoB = D('Auth')->where("auth_id in ($auth_ids) and auth_level=1")->select();//次顶级权限
        }else{ //admin用户获取权限[获取全部权限]
            $auth_infoA = D('Auth')->where("auth_level=0")->select();//顶级权限
            $auth_infoB = D('Auth')->where("auth_level=1")->select();//次顶级权限
        }
        $this -> assign('auth_infoA',$auth_infoA);
        $this -> assign('auth_infoB',$auth_infoB);
        $this -> display();
    }
    public function right(){
        $this -> display();
    }
    public function index(){
        $this -> display();
    }
}
