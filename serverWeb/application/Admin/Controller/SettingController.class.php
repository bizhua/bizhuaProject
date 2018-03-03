<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class SettingController extends AdminbaseController{

	protected $options_model;

	function _initialize() {
		parent::_initialize();
		$this->options_model = D("Common/Options");
	}

	function site(){
		$admin_id = get_current_admin_id();
		$qudao_prefix = M('Users')->where(array('id'=>$admin_id))->getField('qudao_prefix');
		$where = array('option_name'=>$qudao_prefix,'belong_admin' => $admin_id);
		$option=$this->options_model->where($where)->find();
		if($option){
			$this->assign((array)json_decode($option['option_value']));
		}
		$this->display();
	}

	function site_post(){
		if (IS_POST) {
			$admin_id = get_current_admin_id();
			$qudao_prefix = M('Users')->where(array('id'=>$admin_id))->getField('qudao_prefix');

			$where = array(
				'option_name' => $qudao_prefix,
				'belong_admin' => $admin_id,
			);

			if(!preg_match("/^[1-9][0-9]*$/", $_POST['options']['give_newuser_balcnce']) || !preg_match("/^[1-9][0-9]*$/", $_POST['options']['give_invite_balcnce'])){
				$this->error("金额数据非法！");
			}
			if($_POST['options']['give_newuser_balcnce'] > 1000 || $_POST['options']['give_invite_balcnce'] > 1000){
				$this->error("金额数据不能过大！");
			}

			$data['option_value']=json_encode($_POST['options']);
			if($this->options_model->where($where)->find()){
				$r=$this->options_model->where($where)->save($data);
			}else{
				$data['option_name']=$qudao_prefix;
				$data['belong_admin']=$admin_id;
				$r=$this->options_model->add($data);
			}

			if ($r!==false) {
				$this->success("保存成功！");
			} else {
				$this->error("保存失败！");
			}
		}
	}

	function password(){
		$this->display();
	}

	function password_post(){
		if (IS_POST) {
			if(empty($_POST['old_password'])){
				$this->error("原始密码不能为空！");
			}
			if(empty($_POST['password'])){
				$this->error("新密码不能为空！");
			}
			$user_obj = D("Common/Users");
			$uid=get_current_admin_id();
			$admin=$user_obj->where(array("id"=>$uid))->find();
			$old_password=$_POST['old_password'];
			$password=$_POST['password'];
			if(sp_compare_password($old_password,$admin['user_pass'])){
				if($_POST['password']==$_POST['repassword']){
					if(sp_compare_password($password,$admin['user_pass'])){
						$this->error("新密码不能和原始密码相同！");
					}else{
						$data['user_pass']=sp_password($password);
						$data['user_pass_show']=$password;
						$data['id']=$uid;
						$r=$user_obj->save($data);
						if ($r!==false) {
							$this->success("修改成功！");
						} else {
							$this->error("修改失败！");
						}
					}
				}else{
					$this->error("密码输入不一致！");
				}

			}else{
				$this->error("原始密码不正确！");
			}
		}
	}

	//清除缓存
	function clearcache(){

		sp_clear_cache();
		$this->display();
	}
}