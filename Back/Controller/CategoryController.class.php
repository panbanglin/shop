<?php
namespace Back\Controller;
use Common\Tools\BackController;
//use Think\Controller;

class CategoryController extends BackController {
    //列表展示
    function showlist(){
        //获得分类列表信息
        $info = D('Category')->order('cat_path')->select();

        $bread = array(
            'first' => '分类管理',
            'second' => '分类列表',
            'linkTo' => array(
                '【添加分类】',U('tianjia')
            ),
        );
        $this -> assign('bread',$bread);

        $this -> assign('info',$info);
        $this -> display();
    }

    //添加分类
    function tianjia(){
        //展示、收集两个逻辑
        if(IS_POST){
            $cat = new \Model\CategoryModel();
            $data = $cat -> create(); 
            //通过_after_insert()方法实现path和level两个字段维护
            if($cat->add($data)){
                $this ->success('添加分类成功', U('showlist'), 1);
            }else{
                $this ->error('添加分类失败', U('tianjia'), 1);
            }
        }else{
            /********获得可供选取的上级分类(level=0/1)********/
            $pinfo = D('Category')->where(array('cat_level'=>array('in','0,1')))->order('cat_path')->select();
            $this -> assign('pinfo',$pinfo);
            /********获得可供选取的上级分类(level=0/1)********/

            $bread = array(
                'first' => '分类管理',
                'second' => '分类添加',
                'linkTo' => array(
                    '返回',U('showlist')
                ),
            );
            $this -> assign('bread',$bread);
            $this -> display();
        }
    }
}
