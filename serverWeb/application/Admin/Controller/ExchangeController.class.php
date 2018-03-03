<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class ExchangeController extends AdminbaseController {

	//配置
    public function index() {
        $this->assign("app_e",get_options("app_e"));
        $this->assign(get_options("exchange_options"));
    	$this->display();
    }

    //配置处理
    public function index_post() {
    	$_POST = array_map('trim', I('post.'));

    	if(in_array('', $_POST)) $this->error("不能留空！");

    	$data['option_name']="exchange_options";
    	$data['option_value']=json_encode($_POST);
    	if(D("Common/Options")->where("option_name='exchange_options'")->find()){
    		$r=D("Common/Options")->where("option_name='exchange_options'")->save($data);
    	}else{
    		$r=D("Common/Options")->add($data);
    	}

		$buyvip = array(
			'1Month' => I("post.balance_buyvip_1",'','intval'),
			'3Month' => I("post.balance_buyvip_3",'','intval'),
			'12Month' => I("post.balance_buyvip_12",'','intval'),
		);
    	if ($r!==false) {
    		$this->success("保存成功！");
    	} else {
    		$this->error("保存失败！");
    	}
    }
}