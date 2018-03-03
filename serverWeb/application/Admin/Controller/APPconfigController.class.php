<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class APPconfigController extends AdminbaseController{
	
	protected $options_model;
	
	function _initialize() {
		parent::_initialize();
		$this->options_model = D("Common/Options");
	}
	
	function index(){
		$this->assign("app_config",get_options("app_config"));

		$this->assign("cmf_settings",get_options("cmf_settings"));

		$this->assign("app_e",get_options("app_e"));

		$this->assign("sensitiveword_options",get_options("sensitiveword_options"));
		
		$this->display();
	}
	
	function index_post(){
		if (IS_POST) {

			if($_POST['app_config']['start_live_max_price'] < 0 || $_POST['app_config']['start_live_max_minute_charge'] < 0){
				$this->error("金额设置错误");
			}

			if($_POST['app_config']['enable_consume'] == 'YES'){
				if(empty($_POST['app_config']['enable_consume_text']) || $_POST['app_config']['enable_consume_min_balance'] < 0){
					$this->error("持币配置错误");
				}
			}

			$app_config['option_name']="app_config";
			$app_config['option_value']=json_encode($_POST['app_config']);
			if($this->options_model->where("option_name='app_config'")->find()){
				$r=$this->options_model->where("option_name='app_config'")->save($app_config);
			}else{
				$r=$this->options_model->add($app_config);
			}
			
			$banned_usernames=preg_replace("/[^0-9A-Za-z_\x{4e00}-\x{9fa5}-]/u", ",", $_POST['cmf_settings']['banned_usernames']);
			$_POST['cmf_settings']['banned_usernames']=$banned_usernames;

			sp_set_cmf_setting($_POST['cmf_settings']);

			$_POST['sensitiveword_options']['channel_title_word'] = preg_replace("/[^0-9A-Za-z_\x{4e00}-\x{9fa5}-]/u", ",", $_POST['sensitiveword_options']['channel_title_word']);

			$_POST['sensitiveword_options']['chat_content_word'] = preg_replace("/[^0-9A-Za-z_\x{4e00}-\x{9fa5}-]/u", ",", $_POST['sensitiveword_options']['chat_content_word']);

			$sensitiveword_options['option_name']="sensitiveword_options";
			$sensitiveword_options['option_value']=json_encode($_POST['sensitiveword_options']);
			if($this->options_model->where("option_name='sensitiveword_options'")->find()){
				$r=$this->options_model->where("option_name='sensitiveword_options'")->save($sensitiveword_options);
			}else{
				$r=$this->options_model->add($sensitiveword_options);
			}

			
			if ($r!==false) {
				$this->success("保存成功！");
			} else {
				$this->error("保存失败！");
			}
			
		}
	}
		
}