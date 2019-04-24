<?php

//模型

namespace Model;
use Think\Model;

class AttributeModel extends Model{
    // 更新数据前的回调方法
    protected function _before_update(&$data,$options) {}
    // 更新成功后的回调方法
    protected function _after_update($data,$options) {}

    //表单自动验证（由create()方法触发）
    protected $_validate = array(
        //类型验证
        array('type_id','0','类型必须选择',0,'notequal'),
    );
}
