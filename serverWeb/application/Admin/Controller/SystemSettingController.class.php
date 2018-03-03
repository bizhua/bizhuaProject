<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class SystemSettingController extends AdminbaseController {

	//配置
    public function index() {
    	$this->display();
    }
    
    //配置处理
    public function index_post() {
        if(empty($_POST['app_apple_id']) || empty($_POST['bundle_id'])){
            $this->error("APP Apple ID 和Bundle ID不能为空");
        }
        $data = array(
            'APP_Apple_ID' => $_POST['app_apple_id'],
            'IOS_Bundle_ID' => $_POST['bundle_id'],
        );
        
    	$rst=sp_set_dynamic_config($data);
    	sp_clear_cache();
    	if ($rst) {
    		$this->success("保存成功！");
    	} else {
    		$this->error("保存失败！");
    	}
    }
}

