<?php
namespace Back\Controller;
use Common\Tools\BackController;

class AttributeController extends BackController {
    //列表展示
    function showlist(){
        $type_id = I('get.type_id'); //类型id
        //获得属性列表信息
        $info = D('Attribute')->where(array('type_id'=>$type_id))->select();

        /***获得类型信息，给模板实现下拉列表展示***/
        $typeinfo = D('Type')->select();
        $this -> assign('typeinfo',$typeinfo);
        /***获得类型信息，给模板实现下拉列表展示***/

        $bread = array(
            'first' => '属性管理',
            'second' => '属性列表',
            'linkTo' => array(
                '【添加属性】',U('tianjia')
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
            $auth = new \Model\AttributeModel();
            $data = $auth -> create(); 
            if($data){
                if($auth->add($data)){
                    $this ->success('添加属性成功', U('Type/showlist'), 1);
                }else{
                    $this ->error('添加属性失败', U('Attribute/tianjia'), 1);
                }
            }else{
                //验证失败 $data= false
                $errorinfo = $auth->getError(); //获得验证错误信息
                $this ->error($errorinfo, U('tianjia'), 1);
            }
        }else{
            /****可供选择的类型****/
            $typeinfo = D('Type')->select();
            $this -> assign('typeinfo',$typeinfo);
            /****可供选择的类型****/

            $bread = array(
                'first' => '属性管理',
                'second' => '属性添加',
                'linkTo' => array(
                    '类型列表',U('Type/showlist')
                ),
            );
            $this -> assign('bread',$bread);
            $this -> display();
        }
    }

    //根据“类型”获得对应的属性
    function getInfoByType(){
        $type_id = I('get.type_id');
        $info = D('Attribute')->where(array('type_id'=>$type_id))->select();
        echo json_encode($info);
    }
}
