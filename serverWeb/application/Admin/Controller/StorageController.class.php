<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class StorageController extends AdminbaseController{
	
	function _initialize() {
		parent::_initialize();
	}
	function index(){
		$this->assign(get_options("storage_config"));
		$this->display();
	}
	
	function setting_post(){
		if(IS_POST){			
			$support_storages=array("Local","Qiniu");
			$type=$_POST['type'];
			if(in_array($type, $support_storages)){
				if($type == $support_storages[1]){
					if(empty($_POST["type_config"]["accessKey"]) || empty($_POST["type_config"]["secretKey"]) || empty($_POST["type_config"]["domain"]) || empty($_POST["type_config"]["bucket"]) || empty($_POST["type_config"]["upHost"])){
						$this->error("七牛配置不能为空！");
					}else{
						$type_config = $_POST["type_config"];
					}
				}else{
					$type_config = null;
				}
				$temp = array(
					'type' => $type,
					'type_config' => $type_config,
				);
				$data['option_name']="storage_config";
				$data['option_value']=json_encode($temp);
				if(D("Common/Options")->where("option_name='storage_config'")->find()){
					$r=D("Common/Options")->where("option_name='storage_config'")->save($data);
				}else{
					$r=D("Common/Options")->add($data);
				}
				if ($r!==false) {
					$this->success("设置成功！");
				} else {
					$this->error("设置出错！");
				}
			}else{
				$this->error("文件存储类型不存在！");
			}
		
		}
	}
	
	
	
	
	
	
	
}