<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class DeviceWayController extends AdminbaseController {

	public function index() {
		$model = M("DeviceWay");
		$data = $model->order('id ASC')->select();

		$this->assign("data",$data);
		$this->display();
	}

	public function edit(){
		$id=  intval(I("get.id"));
		$info = M("DeviceWay")->where(array('id'=>$id))->find();

		$this->assign("info",$info);
		$this->display();
	}

	function set_machine_info(){
		if(IS_POST){
			$id = intval(I("get.id"));
			$type = I('post.type','','htmlspecialchars');
			$value = I('post.value','','htmlspecialchars');
			if(empty($id) || empty($type) || !isset($value)){
				$this->error("参数错误！");
			}
			$info = M("DeviceWay")->where(array('id'=>$id))->find();
			if(empty($info)){
				$this->error("参数错误！");
			}
			$status = M("DeviceWay")->where(array('id'=>$id))->save(array($type=>$value));
			if($status !== false){
				$this->success("设置成功！");
			}else{
				$this->error("设置失败！");
			}
		}
	}
}