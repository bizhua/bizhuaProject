<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class FeedbackController extends AdminbaseController{
	
	function _initialize() {
		parent::_initialize();
	}
	
	function index(){
		$admin_id = get_current_admin_id();
		$feedback_model = M("Feedback");
		$where = array(
			"a.del_status"=>1,
			'a.belong_admin' => $admin_id,
		);
		if($admin_id == 1){
			unset($where['a.belong_admin']);
		}
		$count = $feedback_model->alias("a")->where($where)->count();
		$page = $this->page($count, 15);
		$data = $feedback_model
		->alias("a")
		->join(C("DB_PREFIX")."users b on b.id = a.uid")
		->where($where)
		->limit($page->firstRow . ',' . $page->listRows)
		->order("a.id DESC,a.status ASC")
		->field("a.*,b.user_login,b.user_nicename")		
		->select();

		$status = array('未回复','已回复');
		
		$this->assign("status",$status);
		$this->assign("data",$data);
		$this->assign("Page", $page->show('Admin'));
		$this->display();		
	}

	//回复反馈
	function reply(){
		$id = I("get.id",0,"intval");
		$admin_id = get_current_admin_id();
		$reply = I("post.reply",'',"htmlspecialchars");
		if(!empty($id)){
			if($admin_id == 1){
				$where = array('id'=>$id);
			}else{
				$where = array('id'=>$id,'belong_admin'=>$admin_id);
			}
			$rst = M("Feedback")->where($where)->setField(array('reply'=>$reply,'status'=>1,'reply_time'=>date('Y-m-d H:i:s')));
			if ($rst) {
				$info = M("Feedback")->where(array('id'=>$id))->find();
				$data = array(
					'title' => '问题反馈回复',
					'content' => '问题：'.$info['content']."    客服回复：".$reply,
					'send_type' => 1,
					'uid' => $info['uid'],
					'make_time' => date('Y-m-d H:i:s'),
					'belong_admin' => $info['belong_admin'],
				);
				M("Message")->add($data);
    			$this->success("回复成功！");
    		} else {
    			$this->error('回复失败！');
    		}
		}else {
    		$this->error('数据传入失败！');
    	}
	}

	//删除
	function delete(){
		$admin_id = get_current_admin_id();
		if(isset($_GET['id'])){
			$id = intval(I("get.id"));
			if($admin_id == 1){
				$where = array('id'=>$id);
			}else{
				$where = array('id'=>$id,'belong_admin'=>$admin_id);
			}
			if (M("Feedback")->where($where)->setField("del_status",0)!==false) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}
		if(isset($_POST['ids'])){
			$ids=join(",",$_POST['ids']);
			if($admin_id == 1){
				$where = "id in ($ids)";
			}else{
				$where = "id in ($ids) and belong_admin = $admin_id";
			}
			if (M("Feedback")->where($where)->setField("del_status",0)!==false) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}
	}
}