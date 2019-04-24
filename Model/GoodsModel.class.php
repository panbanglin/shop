<?php

//商品goodsmodel模型

namespace Model;
use Think\Model;

class GoodsModel extends Model{
    //自动完成设置add_time/upd_time
    protected $_auto = array(
        array('add_time','time',1,'function'),
        array('upd_time','time',3,'function'),
    );

    // 插入数据前的回调方法
    //参数：
    //$data 是收集的表单信息
    //&$data  是“引用”传递，函数内部改变之，外边也可以访问到
    //$options 是设置的各种条件
    protected function _before_insert(&$data,$options) {
        //上传图片处理
        if($_FILES['goods_logo']['error']===0){//图片没有错误才处理
            //1) 上传原图图片
            //通过Think/Upload.class.php实现附件上传
            $cfg = array(
                'rootPath'      =>  './Common/Uploads/', //保存根路径
            );
            $up = new \Think\Upload($cfg);
            $z = $up -> uploadOne($_FILES['goods_logo']);
            //$z会返回成功上传附件的相关信息
            //dump($z);

            //拼装图片的路径名信息，存储到数据库里边
            $big_path_name = $up->rootPath.$z['savepath'].$z['savename'];
            $data['goods_big_logo'] = $big_path_name;

            //2) 根据原图($big_path_name)制作缩略图
            $im = new \Think\Image();//实例化对象
            $im -> open($big_path_name); //打开原图
            $im -> thumb(60,60); //制作缩略图
            //缩略图名字：“small_原图名字”
            $small_path_name = $up->rootPath.$z['savepath']."small_".$z['savename'];
            $im -> save($small_path_name);//存储缩略图到服务器
            //保存缩略图到数据库
            $data['goods_small_logo'] = $small_path_name;
        }
    }

    // 插入成功后的回调方法
    protected function _after_insert($data,$options) {

        //收集属性并存储
        if(!empty($_POST['attr_info'])){
            //变量每个属性信息
            foreach($_POST['attr_info'] as $k => $v){
                //$k代表attr_id
                foreach($v as $kk => $vv){
                    //$vv代表每个属性的值
                    $arr = array(
                        'goods_id' => $data['goods_id'],
                        'attr_id'  => $k,
                        'attr_value' => $vv
                    );
                    D('GoodsAttr')->add($arr);
                }
            }
        }

        //扩展分类信息收集存储
        if(!empty($_POST['cat_ext_info'])){
            foreach($_POST['cat_ext_info'] as $k => $v){
                $cat_arr = array(
                    'goods_id' => $data['goods_id'],
                    'cat_id' => $v,
                );
                D('GoodsCat')->add($cat_arr);
            }
        }

        //上传相册图片判断（只要有一个相册上传，就往下进行）
        $flag = false;
        foreach($_FILES['goods_pics']['error'] as $a => $b){
            if($b === 0){
                $flag = true;
                break;
            }
        }
        if($flag === true){
            //商品相册图片上传
            $cfg = array(
                'rootPath'      =>  './Common/Pics/', //保存根路径
            );
            //dump($_FILES);
            $up = new \Think\Upload($cfg);
            $z = $up -> upload(array('goods_pics'=>$_FILES['goods_pics']));
            //通过返回值$z可以看到对应的上传ok的附件信息
            //dump($z);

            //遍历$z,获得每个附件的信息，存储到数据表中goods_pics
            foreach($z as $k => $v){
                $pics_big_name = $up->rootPath.$v['savepath'].$v['savename'];
                
                /******根据大图，制作缩略图******/
                $im = new \Think\Image();//实例化对象
                $im -> open($pics_big_name); //打开原图
                $im -> thumb(60,60); //制作缩略图
                //缩略图名字：“small_原图名字”
                $pics_small_name = $up->rootPath.$v['savepath']."small_".$v['savename'];
                $im -> save($pics_small_name);//存储缩略图到服务器
                /******根据大图，制作缩略图******/

                //goods_id（$data['goods_id']）, pics_big, pics_small
                $arr = array(
                    'goods_id' => $data['goods_id'],
                    'pics_big' => $pics_big_name,
                    'pics_small' => $pics_small_name,
                );
                //实现相册存储
                D('GoodsPics')->add($arr);
            }
        }
    }

    //给后台获得商品列表信息，有"分页"要求
    function fetchData(){
        //① 获得商品总条数
        $total = $this -> count();
        $per = 5;

        //② 实例化分页类Page对象
        $page = new \Common\Tools\Page($total,$per);

        //③ 获得分页信息
        $pageinfo = $this ->where(array('is_del'=>'不删除'))-> order('goods_id desc')->limit($page->offset,$per)->select();

        //④ 获得页码列表信息
        $pagelist = $page -> fpage(array(3,4,5,6,7,8));

        return array(
            'pageinfo'=>$pageinfo,
            'pagelist'=>$pagelist
        );
    }

    // 更新数据前的回调方法
    protected function _before_update(&$data,$options) {
        /****************logo图片处理******************/
        //判断是否有上传logo图片，并做处理
        if($_FILES['goods_logo_upd']['error'] === 0){
            //1)删除该商品原先的物理图片
            $logoinfo = $this->field('goods_big_logo,goods_small_logo')->find($options['where']['goods_id']);
            if(!empty($logoinfo['goods_big_logo']) || !empty($logoinfo['goods_small_logo'])){
                dump(1234);
                unlink($logoinfo['goods_big_logo']);
                unlink($logoinfo['goods_small_logo']);
            }

            //2) 上传原图图片
            //通过Think/Upload.class.php实现附件上传
            $cfg = array(
                'rootPath'      =>  './Common/Uploads/', //保存根路径
            );
            $up = new \Think\Upload($cfg);
            $z = $up -> uploadOne($_FILES['goods_logo_upd']);
            //$z会返回成功上传附件的相关信息
            //dump($z);

            //拼装图片的路径名信息，存储到数据库里边
            $big_path_name = $up->rootPath.$z['savepath'].$z['savename'];
            $data['goods_big_logo'] = $big_path_name;

            //3) 根据原图($big_path_name)制作缩略图
            $im = new \Think\Image();//实例化对象
            $im -> open($big_path_name); //打开原图
            $im -> thumb(60,60); //制作缩略图
            //缩略图名字：“small_原图名字”
            $small_path_name = $up->rootPath.$z['savepath']."small_".$z['savename'];
            $im -> save($small_path_name);//存储缩略图到服务器
            //保存缩略图到数据库
            $data['goods_small_logo'] = $small_path_name;
        }
        /****************logo图片处理******************/

        /****************相册图片处理******************/
        //判断相册图片
        //上传相册图片判断（只要有一个相册上传，就往下进行）
        $flag = false;
        foreach($_FILES['goods_pics_upd']['error'] as $a => $b){
            if($b === 0){
                $flag = true;
                break;
            }
        }
        if($flag === true){
            //商品相册图片上传
            $cfg = array(
                'rootPath'      =>  './Common/Pics/', //保存根路径
            );
            //dump($_FILES);
            $up = new \Think\Upload($cfg);
            $z = $up -> upload(array('goods_pics_upd'=>$_FILES['goods_pics_upd']));
            //通过返回值$z可以看到对应的上传ok的附件信息

            //遍历$z,获得每个附件的信息，存储到数据表中goods_pics
            foreach($z as $k => $v){
                $pics_big_name = $up->rootPath.$v['savepath'].$v['savename'];
                
                /******根据大图，制作缩略图******/
                $im = new \Think\Image();//实例化对象
                $im -> open($pics_big_name); //打开原图
                $im -> thumb(60,60); //制作缩略图
                //缩略图名字：“small_原图名字”
                $pics_small_name = $up->rootPath.$v['savepath']."small_".$v['savename'];
                $im -> save($pics_small_name);//存储缩略图到服务器
                /******根据大图，制作缩略图******/

                $arr = array(
                    'goods_id' => $options['where']['goods_id'],
                    'pics_big' => $pics_big_name,
                    'pics_small' => $pics_small_name,
                );
                //实现相册存储
                D('GoodsPics')->add($arr);
            }
        }
        /****************相册图片处理******************/
    }
    // 更新成功后的回调方法
    protected function _after_update($data,$options) {
        /*************属性处理*************/
        //商品 [类型]、属性 的更新操作
        //战略：delete删除旧的全部属性，insert写入新的属性
        //① 删除旧的属性
        D('GoodsAttr')->where(array('goods_id'=>$data['goods_id']))->delete();
        //② 写入新的属性
        //收集属性并存储
        if(!empty($_POST['attr_info'])){
            //变量每个属性信息
            foreach($_POST['attr_info'] as $k => $v){
                //$k代表attr_id
                foreach($v as $kk => $vv){
                    //$vv代表每个属性的值
                    $arr = array(
                        'goods_id' => $data['goods_id'],
                        'attr_id'  => $k,
                        'attr_value' => $vv
                    );
                    D('GoodsAttr')->add($arr);
                }
            }
        }
        /*************属性处理*************/
        /*************扩展分类处理**********/
        //删除旧的分类、添加新的分类
        D('GoodsCat')->where(array('goods_id'=>$data['goods_id']))->delete();
        //扩展分类信息收集存储
        if(!empty($_POST['cat_ext_info'])){
            foreach($_POST['cat_ext_info'] as $k => $v){
                $cat_arr = array(
                    'goods_id' => $data['goods_id'],
                    'cat_id' => $v,
                );
                $z = D('GoodsCat')->add($cat_arr);
            }
        }
        /*************扩展分类处理**********/
    }
}
