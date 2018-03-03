<?php
// +----------------------------------------------------------------------
// +----------------------------------------------------------------------
/**
 * 直播配置
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class LiveController extends AdminbaseController {

	//配置
    public function index() {
    	$this->display();
    }
    
    //配置处理
    public function index_post() {
    	$_POST = array_map('trim', I('post.'));
    	if(in_array('', $_POST)) $this->error("不能留空！");
    	$configs['SP_PLAY_HOST'] = $_POST['play_host'];
     	$configs['SP_PUBLISH_HOST'] = $_POST['publish_host'];
    	$configs['SP_HLS_HOST'] = $_POST['hls_host'];
    	$configs['SP_HLS_EXT'] = $_POST['hls_ext'];

        $configs2['APPID'] = $_POST['leancloud_appid'];
        $configs2['APPKEY'] = $_POST['leancloud_appkey'];
        $configs2['MASTERKEY'] = $_POST['leancloud_masterkey'];

        $data = array('DL_LIVE_SETTING'=>$configs,'DL_LeanCloud'=>$configs2);
    	$rst=sp_set_dynamic_config($data);
    	sp_clear_cache();
    	if ($rst) {
    		$this->success("保存成功！");
    	} else {
    		$this->error("保存失败！");
    	}
    }
	
}