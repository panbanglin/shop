<?php
namespace Home\Controller;
use Model\UserModel;
use Think\Controller;
class UserController extends Controller {
    public function login(){
        $user = new UserModel();
        if(IS_POST){
            $username = $_POST['username'];
            $password = $_POST['password'];
            $password = md5($password);
            $info = $user->where(array('user_name'=>$username, 'user_pwd'=>$password))->find();
            if($info){
                session('user_name', $info['user_name']);
                session('user_id', $info['user_id']);
                $this->redirect('Index/index');
            }else{
                $this->error('账号或密码错误', U('login'), 2);
            }
        }else{

            $this -> display();
        }
    }
    public function logout(){
        session(null);
        $this->redirect('Index/index');
    }
    public function regist(){
        $user = new UserModel();
        if(IS_POST){
            $data = $user->create();
            if($user->add($data)){
                $this->success('注册成功', U('showRegister'), 2);
            }else{
                $this->error('注册失败', U('regist'), 2);
            }
        }else{

            $this -> display();
        }
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
    public function showRegister(){
        $this->display();

    }
    //会员邮箱激活
    public function jihuo(){
        $user_id = I('get.user_id');
        $user_check_code = I('get.checkcode');
        //更改user_check
        $user = D('User');
        //验证，id与checkcode是否对应
        $info = $user->where(array('user_check'=>0))->find($user_id);
        if($info['user_check_code'] == $user_check_code){
            $z = $user->setField(array('user_id'=>$user_id, 'user_check'=>1, 'user_check_code' =>''));
            if($z){
                $this->success('激活成功', U('login'),1);
            }
        }else{
            $this->error('操作有错误，账号已激活', U('login'),1);
        }

    }
}
