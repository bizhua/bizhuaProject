<?php

namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class APPPushController extends AdminbaseController {

	public function index() {
		$this->display();
	}


	public function send(){
		$type = I('post.type','','htmlspecialchars');
		$title = I('post.title','','htmlspecialchars');
		$object = I('post.object','','htmlspecialchars');

		$status = sp_send_jpush($type,$title,$object,3600*24);
		if($status == 'success'){
			$admin_id = sp_get_current_admin_id();
			$data = array(
				'type' => $type,
				'title' => $title,
				'object' => $object,
				'push_time' => date('Y-m-d H:i:s'),
				'belong_admin' => $admin_id,
			);
			M("PushLogs")->add($data);
			$this->success("推送成功");
		}else{
			$this->error($status);
		}
	}

	public function record(){
		$admin_id = sp_get_current_admin_id();
		$push_log_model = M("PushLogs");
		$where['status'] = 1;
		$where['belong_admin'] = $admin_id;

		$count = $push_log_model->where($where)->count();

		$page = $this->page($count, 15);

		$data = $push_log_model->where($where)->limit($page->firstRow . ',' . $page->listRows)
		->order("id DESC")->select();

		$this->assign("data",$data);
		$this->assign("Page", $page->show('Admin'));
		$this->assign("current_page",$page->GetCurrentPage());
		$this->display();
	}
}

