<?php

//模型

namespace Model;
use Think\Model;

class UserModel extends Model{
    // 字段映射定义
    //将form表单中自定义字段映射成数据库字段
    protected $_map = array(
        'username' => 'user_name',
        'password' => 'user_pwd',
        'email' => 'user_email'
    );
    // 自动完成定义
    protected $_auto = array(
        array('add_time', 'time', 1, 'function'),   //完成add_time字段的填充
        array('user_pwd', 'md5', 1, 'function'),   //完成user_pwd字段的填充
    );
    // 更新数据前的回调方法
    protected function _before_insert(&$data,$options) {}
    // 增加数据成功后的回调方法
    protected function _after_insert($data,$options) {
        if($_POST['act'] == 'regist'){

            //生成唯一校验码
            $code = md5(uniqid());
            $this->setField(array("user_id"=>$data['user_id'], 'user_check_code'=>$code));  //更新校验码
            $link = "http://localhost/shop/index.php/Home/User/jihuo/user_id/{$data['user_id']}/checkcode/$code/";
            //发送激活邮件
            sendEmail($data['user_email'], '会员注册激活', "请点击一下超链接激活您的账号<a href='".$link."' >$link</a>");
        }
    }
}
