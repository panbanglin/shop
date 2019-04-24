<?php
namespace Model;
use Think\Model;

class CategoryModel extends Model{
    // 插入成功后的回调方法
    protected function _after_insert($data,$options) {
        //维护两个字段：cat_path/cat_level
        //1) 维护cat_path
        //① 顶级分类：等于 “主键id值”
        if($data['cat_pid'] == 0){
            $path = $data['cat_id'];
        }else{
        //② 非顶级分类：等于 “父级全路径-主键id值”
            //获得父级全路径
            $pinfo = $this ->field('cat_path')->find($data['cat_pid']);
            $ppath = $pinfo['cat_path'];

            $path = $ppath."-".$data['cat_id'];
        }

        //2) 维护等级cat_level,等于 全路径"-"横杠的个数
        $level = substr_count($path,'-'); //计算$path内部"-横杠"的个数

        $this -> save(array('cat_id'=>$data['cat_id'],'cat_path'=>$path,'cat_level'=>$level));
        
    }
}
