<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $cat_infoA = D('Category')->where(array('cat_level'=>0))->select();
        $cat_infoB = D('Category')->where(array('cat_level'=>1))->select();
        $cat_infoC = D('Category')->where(array('cat_level'=>2))->select();
        $this->assign('cat_infoA', $cat_infoA);
        $this->assign('cat_infoB', $cat_infoB);
        $this->assign('cat_infoC', $cat_infoC);
        $this -> display();
    }
}