<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class RedisController extends AdminbaseController {

	//配置
    public function index() {
    	$this->display();
    }
    
    //配置处理
    public function index_post() {
    	$_POST = array_map('trim', I('post.'));
    	//if(in_array('', $_POST)) $this->error("不能留空！");
        $configs['Host'] = $_POST['host'];
    	$configs['Port'] = $_POST['port'];
     	$configs['DB_num'] = $_POST['db_num'];
        $configs['Auth'] = $_POST['auth'];

        $data = array('DL_Redis'=>$configs);
    	$rst=sp_set_dynamic_config($data);
    	sp_clear_cache();
    	if ($rst) {
    		$this->success("保存成功！");
    	} else {
    		$this->error("保存失败！");
    	}
    }
	
}