<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class ReportController extends AdminbaseController{
	
	function _initialize() {
		parent::_initialize();
	}
	
	function index(){
		$report_model = M("Report");
		$where = array(
			"a.del_status"=>1,
		);
		$count = $report_model->alias("a")->where($where)->count();
		$page = $this->page($count, 15);
		$data = $report_model
		->alias("a")
		->join(C("DB_PREFIX")."users b on b.id = a.uid")
		->join(C("DB_PREFIX")."users c on c.id = a.owner")
		->where($where)
		->limit($page->firstRow . ',' . $page->listRows)
		->order("a.id DESC,a.status DESC")
		->field("a.id,a.uid,a.type,a.object_id,a.owner,a.text,a.img,a.add_time,a.status,b.user_login as user_login1,b.user_nicename as user_nicename1,c.user_login as user_login2,c.user_nicename as user_nicename2")
		->select();

		$type = array('','直播举报');
		$status = array('已处理',"<font color='#FF0000'>未处理</font>");
		
		$this->assign("type",$type);
		$this->assign("status",$status);
		$this->assign("data",$data);
		$this->assign("Page", $page->show('Admin'));
		$this->display();
	}
	
	function delete(){
		if(isset($_GET['id'])){
			$id = intval(I("get.id"));
			if (M("Report")->where("id=$id")->setField("del_status",0)!==false) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}
		if(isset($_POST['ids'])){
			$ids=join(",",$_POST['ids']);
			if (M("Report")->where("id in ($ids)")->setField("del_status",0)!==false) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}
	}

	//处理，修改状态
	function deal(){
		if(isset($_GET['id'])){
			$id = intval(I("get.id"));
			if (M("Report")->where("id=$id")->setField("status",0)!==false) {
				$this->success("修改成功！");
			} else {
				$this->error("修改失败！");
			}
		}
		if(isset($_POST['ids'])){
			$ids=join(",",$_POST['ids']);
			if (M("Report")->where("id in ($ids)")->setField("status",0)!==false) {
				$this->success("修改成功！");
			} else {
				$this->error("修改失败！");
			}
		}
	}
}