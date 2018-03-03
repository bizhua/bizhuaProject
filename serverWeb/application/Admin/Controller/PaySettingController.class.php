<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class PaySettingController extends AdminbaseController {
	
	//设置
	function index(){
		$admin_id = get_current_admin_id();
		$app_e = get_options("app_e");
		if($app_e['14'] != 1){
			$where['class_name'] = array('neq', 'rechargecard');
			$where['belong_admin'] = $admin_id;
		}else{
			$where = array('belong_admin'=>$admin_id);
		}
		$model = M("Paysetting");
		$count = $model->where($where)->count();
		$page = $this->page($count, 15);
		$data = $model->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
		$status = array('未启用','启用');
		$type = array('否','是');
		$this->assign("status",$status);
		$this->assign("type",$type);
		$this->assign("data",$data);
		$this->assign("Page", $page->show('Admin'));
		$this->assign("app_e", $app_e);
		$this->display();
	}
	
	//设置
	function add(){
		//权限检查
        // if (sp_get_current_admin_id() != 1) {
        //     //如果不是超级管理员，禁止添加和修改
        //     $this->error("没有权限");
        // }
        $this->assign("app_e", get_options("app_e"));
		$this->display();
	}

	function add_post(){
		if(IS_POST){
			if(empty($_POST['name']) || empty($_POST['mid']) || empty($_POST['key']) || empty($_POST['class_name']) || empty($_POST['icon'])){
				$this->error("名称，商户编号，密钥，支付类名称和图标不能为空");
			}
			if (M("Paysetting")->create()){
				$admin_id = get_current_admin_id();
				$class_name = $_POST['class_name'];
				$temp = M("Paysetting")->where(array('class_name'=>$class_name,'belong_admin'=>$admin_id))->find();
				if($temp){
					$this->error("支付类名称已存在");
				}
				if (M("Paysetting")->add()!==false) {
					$this->success(L('ADD_SUCCESS'), U("Admin/PaySetting/index"));
				} else {
					$this->error(L('ADD_FAILED'));
				}
			} else {
				$this->error(M("Paysetting")->getError());
			}
		
		}
	}

	function edit(){
		//权限检查
        // if (sp_get_current_admin_id() != 1) {
        //     //如果不是超级管理员，禁止添加和修改
        //     $this->error("没有权限");
        // }
		$id=I("get.id");
		$admin_id = get_current_admin_id();
		$ad=M("Paysetting")->where(array('id'=>$id,'belong_admin'=>$admin_id))->find();
		$this->assign($ad);
		$this->assign("app_e", get_options("app_e"));
		$this->display();
	}
	
	function edit_post(){
		if (IS_POST) {
			if(empty($_POST['name']) || empty($_POST['mid']) || empty($_POST['key']) || empty($_POST['class_name']) || empty($_POST['icon'])){
				$this->error("名称，商户编号，密钥，支付类名称和图标不能为空");
			}
			if (M("Paysetting")->create()) {
				$admin_id = get_current_admin_id();
				$class_name = $_POST['class_name'];
				if (M("Paysetting")->where(array('class_name'=>$class_name,'belong_admin'=>$admin_id))->save()!==false) {
					$this->success("保存成功！", U("Admin/PaySetting/index"));
				} else {
					$this->error("保存失败！");
				}
			} else {
				$this->error(M("Paysetting")->getError());
			}
		}
	}

	/**
	 *  删除
	 */
	function delete(){
		//权限检查
        // if (sp_get_current_admin_id() != 1) {
        //     //如果不是超级管理员，禁止添加和修改
        //     $this->error("没有权限");
        // }
		$id = I("get.id",0,"intval");
		$admin_id = get_current_admin_id();
		if($id < 3){
			$this->error("支付宝和微信支付不能删除，不用时请禁用");
		}
		if (M("Paysetting")->where(array('belong_admin'=>$admin_id))->delete($id)!==false) {
			$this->success("删除成功！");
		} else {
			$this->error("删除失败！");
		}
	}

	function status(){
		//权限检查
        // if (sp_get_current_admin_id() != 1) {
        //     //如果不是超级管理员，禁止添加和修改
        //     $this->error("没有权限");
        // }
		if(isset($_POST['ids']) && $_GET["enable"]){
			$data["status"]=1;

			$ids=join(",", $_POST['ids']);
			$admin_id = get_current_admin_id();
			if ( M("Paysetting")->where("id in ($ids) and belong_admin = $admin_id")->save($data)!==false) {
				$this->success("启用成功！");
			} else {
				$this->error("启用失败！");
			}
		}
		if(isset($_POST['ids']) && $_GET["disable"]){
			$data["status"]=0;

			$ids=join(",", $_POST['ids']);
			$admin_id = get_current_admin_id();
			if ( M("Paysetting")->where("id in ($ids) and belong_admin = $admin_id")->save($data)!==false) {
				$this->success("禁用成功！");
			} else {
				$this->error("禁用失败！");
			}
		}
	}

	public function sort(){
		//权限检查
        // if (sp_get_current_admin_id() != 1) {
        //     //如果不是超级管理员，禁止添加和修改
        //     $this->error("没有权限");
        // }
		$status = parent::_listorders(M("Paysetting"));
		if ($status) {
			$this->success("排序更新成功！");
		} else {
			$this->error("排序更新失败！");
		}
	}

}