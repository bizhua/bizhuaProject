<?php
namespace Admin\Controller;
use Common\Controller\AdbaseController;
class AdController extends AdbaseController{
	protected $ad_model;
	
	function _initialize() {
		parent::_initialize();
		if(empty($_GET['sign']) && empty($_SESSION['sign'])){
			$this->error("没有权限");
		}
		$host = $_SERVER['HTTP_HOST'];
		if($_GET['sign'] != md5($host) && $_SESSION['sign'] != md5($host)){
			$this->error("没有权限");
		}
		if(!empty($_GET['sign'])){
			session('sign',$_GET['sign']);
		}else{
			session('sign',$_SESSION['sign']);
		}		
		$this->ad_model = D("Common/Ad");
	}
	
	function index(){
		$ads=$this->ad_model->select();
		$app_e = get_options("app_e");
		if(empty($app_e)){
			$this->error("APP功能异常");
		}
		$this->assign('app_e',empty($app_e['12']) ? 0 : $app_e['12']);
		$this->assign("ads",$ads);

		$type = array('','直播间广告','启动广告','列表广告');
		$this->assign("type",$type);
		$this->display();
	}
	
	function add(){
		$this->display();
	}
	
	function add_post(){
		if(IS_POST){
			if ($this->ad_model->create()){
				if ($this->ad_model->add()!==false) {
					$this->success(L('ADD_SUCCESS'), U("ad/index"));
				} else {
					$this->error(L('ADD_FAILED'));
				}
			} else {
				$this->error($this->ad_model->getError());
			}
		
		}
	}
	
	function edit(){
		$id=I("get.id");
		$ad=$this->ad_model->where("ad_id=$id")->find();
		$this->assign($ad);
		$this->display();
	}
	
	function edit_post(){
		if (IS_POST) {
			if ($this->ad_model->create()) {
				if ($this->ad_model->save()!==false) {
					$this->success("保存成功！", U("ad/index"));
				} else {
					$this->error("保存失败！");
				}
			} else {
				$this->error($this->ad_model->getError());
			}
		}
	}
	
	/**
	 *  删除
	 */
	function delete(){
		$id = I("get.id",0,"intval");
		if ($this->ad_model->delete($id)!==false) {
			$this->success("删除成功！");
		} else {
			$this->error("删除失败！");
		}
	}
	
	function toggle(){
		if(isset($_POST['ids']) && $_GET["display"]){
			$ids = implode(",", $_POST['ids']);
			$data['status']=1;
			if ($this->ad_model->where("ad_id in ($ids)")->save($data)!==false) {
				$this->success("显示成功！");
			} else {
				$this->error("显示失败！");
			}
		}
		if(isset($_POST['ids']) && $_GET["hide"]){
			$ids = implode(",", $_POST['ids']);
			$data['status']=0;
			if ($this->ad_model->where("ad_id in ($ids)")->save($data)!==false) {
				$this->success("隐藏成功！");
			} else {
				$this->error("隐藏失败！");
			}
		}
	}
	
	function status(){
		if($_GET["hide"]){
			$status = 0;
		}else{
			$status = 1;
		}
		$app_e = get_options("app_e");
		if(empty($app_e)){
			$this->error("APP功能异常");
		}else{
			$app_e['12'] = $status;
			if(M("options")->where(array('option_name'=>'app_e'))->setField('option_value',json_encode($app_e)) !== false){
				$this->success("设置成功！");
			} else {
				$this->error("设置失败！");
			}
		}
	}
	
	function app_e(){
		$app_e = get_options("app_e");
		if(empty($app_e)){
			$this->error("APP功能异常");
		}
		$this->assign('key',array_keys($app_e));
		$this->assign('app_e',$app_e);
		$this->display();
	}

	function app_e_post(){
		if(IS_POST){
			if(M("options")->where(array('option_name'=>'app_e'))->setField('option_value',json_encode($_POST)) !== false){
				$this->success("设置成功！");
			} else {
				$this->error("设置失败！");
			}
		}
	}
	
}