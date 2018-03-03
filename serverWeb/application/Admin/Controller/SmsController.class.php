<?php
// +----------------------------------------------------------------------
// +----------------------------------------------------------------------
/**
 * sms配置
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class SmsController extends AdminbaseController {

	//sms配置
    public function index() {
    	$this->display();
    }
    
    //sms配置处理
    public function index_post() {
    	$_POST = array_map('trim', I('post.'));
    	if(in_array('', $_POST)) $this->error("不能留空！");
    	$configs['SP_SMS_KEY'] = $_POST['appkey'];
    	$configs['SP_SMS_TPL'] = $_POST['tpl_id'];
    	$rst=sp_set_dynamic_config($configs);
    	sp_clear_cache();
    	if ($rst) {
    		$this->success("保存成功！");
    	} else {
    		$this->error("保存失败！");
    	}
    }
}

