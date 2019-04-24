<?php

//后台父类控制器
namespace Common\Tools;
use Think\Controller;

class BackController extends Controller{
    //构造方法
    function __construct(){
        parent::__construct();//先执行父类的，否则父类构造方法被覆盖
        $admin_id = session('admin_id');
        $admin_name = session('admin_name');
        //权限控制过滤功能实现
        $nowAC = CONTROLLER_NAME."-".ACTION_NAME;
        if(!empty($admin_name)){//登录状态
            //系统有一些权限无需分配，直接可以访问
            $allowAC = "Admin-logout,Admin-login,Admin-verifyImg,Admin-checkCode,Index-index,Index-head,Index-left,Index-right";
            //获得当前管理员对应角色的"role_auth_ac"信息
            $roleinfo = D('Manager')->alias('m')->join('__ROLE__ r on m.mg_role_id=r.role_id')->field('r.role_auth_ac')->where(array('mg_id'=>$admin_id))->find();
            $role_ac = $roleinfo['role_auth_ac'];

            //判断当前访问的"权限"是否是角色允许的“权限”
            //① 访问的权限在角色权限列表没有出现
            //② 访问的权限在默认允许的列表也没有出现
            //③ 当前用户还不是admin超级管理员
            //以上① 、② 、③ 同时满足，就是“没有权限访问”
            //strpos(s1,s2),判断s2在s1首次出现位置(0/1/2/3..),没有出现返回false
            if(strpos($role_ac,$nowAC)===false && strpos($allowAC,$nowAC)===false && $admin_name!='admin'){
                exit('没有权限访问！');
            }
        }else{//退出系统状态
            //跳转到登录页去
            //退出状态也让访问的权限定义
            $allowAC = "Admin-login,Admin-verifyImg,Admin-checkCode";
            if(strpos($allowAC,$nowAC)===false){
                //$this -> redirect('Admin/login');

                //通过js，可以使得全部的frameset都跳转
                $js = <<<eof
                    <script type="text/javascript">
                    window.top.location.href = "/index.php/Back/Admin/login";
                    </script>
eof;
                echo $js;
            }
        }

    }
}
