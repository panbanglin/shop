<?php
namespace Back\Controller;
use Common\Tools\BackController;
//use Think\Controller;

class AdminController extends BackController {
    public function login(){
        layout(false);//不使用布局
        if(IS_POST){
            //dump($_POST);
            //array(3) {
            //  ["admin_user"] => string(3) "tom"
            //  ["admin_psd"] => string(3) "123"
            //  ["captcha"] => string(4) "ffjb"
            //}
            $name = $_POST['admin_user'];
            $pwd = $_POST['admin_psd'];
            //用户名和密码的校验
            $manager = D('Manager');
            $info = $manager ->where(array('mg_name'=>$name,'mg_pwd'=>$pwd))-> find();
            if($info != null){
                //session持久化操作（id/name）
                session('admin_id',$info['mg_id']);
                session('admin_name',$info['mg_name']);
                //页面跳转
                $this -> redirect('Index/index');
            }
            $this -> error('用户名或密码错误',U('login'),2);
        }else{
            $this -> display();
        }
    }

    //管理员退出系统
    public function logout(){
        session(null); //清空session
        //$this -> redirect('[分组/控制器/]操作方法');
        $this -> redirect('login'); //页面跳转到登录页
    }

    public function verifyImg(){
        //显示验证码
        $cfg = array(
            'imageH'    =>  40,               // 验证码图片高度
            'imageW'    =>  100,               // 验证码图片宽度
            'length'    =>  4,               // 验证码位数
            'fontttf'   =>  '4.ttf',              // 验证码字体，不设置随机获取
            'fontSize'  =>  15,              // 验证码字体大小(px)
        );
        $very = new \Think\Verify($cfg);
        $very -> entry();
    }

    //ajax过来校验验证码
    function checkCode(){
        $code = I('get.code'); //获得用户输入的验证码
        $vry = new \Think\Verify();
        if($vry -> check($code)){
            echo json_encode(array('status'=>1));
        }else{
            echo json_encode(array('status'=>2));
        }
    }

    //管理员列表展示
    function showlist(){
        $bread = array(
            'first' => '管理员管理',
            'second' => '管理员列表',
            'linkTo' => array(
                '【添加管理员】',U('Admin/tianjia')
            ),
        );
        $this -> assign('bread',$bread);

        $info = D('Manager')->alias('m')->join('LEFT JOIN __ROLE__ r on m.mg_role_id=r.role_id')->field('m.*,r.role_name')->select();
        //SELECT m.*,r.role_name FROM php41_manager m LEFT JOIN php41_role r on m.mg_role_id=r.role_id
        $this -> assign('info',$info);
        $this -> display();
    }
}
