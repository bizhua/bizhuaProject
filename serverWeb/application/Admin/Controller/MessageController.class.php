<?php

namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class MessageController extends AdminbaseController {

	function _initialize() {
		parent::_initialize();
	}


	public function index() {
		//显示发布过的站内信
		$message = M("Message");
		$admin_id = get_current_admin_id();
		$count = $message->where(array('status'=>1,'belong_admin'=>$admin_id))->order('make_time desc')->count();
		$page = $this->page($count, 15);
		$messages = $message->limit($page->firstRow . ',' . $page->listRows)->where(array('status'=>1,'belong_admin'=>$admin_id))->order('make_time desc')->select();
		$this->assign("Page", $page->show("Admin"));
		$send_typename = array('全体用户','指定用户');
		$this->assign("send_typename",$send_typename);
		$this->assign("messages",$messages);
		$this->display();
	}

	function add(){
		$this->display();
	}


	function add_post(){
		//发布信息
		if(IS_POST){
			if(empty($_POST["title"]))
				$this->error("标题不能为空！");

			if(empty($_POST["content"]))
				$this->error("内容不能为空");

			// if($_POST["send_type"]!='1'&&$_POST["send_type"]!='0')
			// 	$this->error("发送类型错误！");

			$info = M("Message");
			$admin_id = get_current_admin_id();
			$message["title"] = I("post.title");
			$message["content"] = I("post.content");
			$message["send_type"] = I("post.send_type");

			if($message["send_type"] =='1')
			{
				$uid = $_POST["user_login"];
				if($uid < 1)
					$this->error("指定用户不存在！");
				$user_info = M("Users")->where(array('id'=>$uid,'user_status'=>1,'belong_admin'=>$admin_id))->getField('id');
				if(empty($user_info)){
					$this->error("指定用户不存在！");
				}
				$message["uid"] = $uid;				
			}
			$message["make_time"] =  date('Y-m-d H:i:s',time());
			$message["belong_admin"] = $admin_id;

			if($info->where()->add($message)){
				$this->success("添加成功");
			}else{
				$this->error("添加成功");
			}
		}else{
			$this->error("参数有误");
		}
	}

	function edit(){
		if(isset($_GET["id"])){
			$id = I("get.id");
			$admin_id = get_current_admin_id();
			$message_model = M("Message");
			$messages = $message_model->where(array("id"=>$id,'belong_admin'=>$admin_id))->find();
			if(empty($data)){
				$this->assign($messages);
				$this->display();
			}else{
				$this->error("参数错误！");
			}
		}else{
			$this->error("参数错误！");
		}
	}

	function edit_post(){
		//发布信息
		if(IS_POST){
			$id = I("post.id");
			$admin_id = get_current_admin_id();
			if(empty($_POST["title"]))
				$this->error("标题不能为空！");

			if(empty($_POST["content"]))
				$this->error("内容不能为空");

			$info = M("Message");
			$message["title"] = I("post.title");
			$message["content"] = I("post.content");
			$message["make_time"] =  date('Y-m-d H:i:s',time());
			if($info->where(array("id"=>$id,'belong_admin'=>$admin_id))->save($message)){
				$this->success("修改成功！", U("Message/index"));
			}else{
				$this->error("修改失败！");
			}
		}else{
			$this->error("请重新提交！");
		}
	}

	function delete(){
		//删除站内信
		if(isset($_GET["id"])){
			$id = I("get.id");
			$admin_id = get_current_admin_id();
			$message_model = M("Message");
			$data['status'] = 0 ;
			if($message_model->where(array("id"=>$id,'belong_admin'=>$admin_id))->save($data)){
				$this->success("删除成功！");
			}else{
				$this->error("删除失败！");
			}
		}else{
			$this->error("参数错误！");
		}
	}

}

