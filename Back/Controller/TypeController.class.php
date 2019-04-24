<?php
namespace Back\Controller;
use Common\Tools\BackController;

class TypeController extends BackController {
    //列表展示
    function showlist(){
        //获得类型列表信息
        $info = D('Type')->select();

        $bread = array(
            'first' => '类型管理',
            'second' => '类型列表',
            'linkTo' => array(
                '【添加类型】',U('tianjia')
            ),
        );
        $this -> assign('bread',$bread);

        $this -> assign('info',$info);
        $this -> display();
    }

    //添加权限
    function tianjia(){
        //展示、收集两个逻辑
        if(IS_POST){
            $auth = new \Model\TypeModel();
            $data = $auth -> create(); 
            //通过_after_insert()方法实现path和level两个字段维护
            if($auth->add($data)){
                $this ->success('添加类型成功', U('showlist'), 2);
            }else{
                $this ->error('添加类型失败', U('tianjia'), 2);
            }
        }else{
            $bread = array(
                'first' => '类型管理',
                'second' => '类型添加',
                'linkTo' => array(
                    '返回',U('showlist')
                ),
            );
            $this -> assign('bread',$bread);
            $this -> display();
        }
    }
}
