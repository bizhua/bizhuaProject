<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class PriceController extends AdminbaseController {
	
	protected $vip_prices_model;
	
	function _initialize() {
		parent::_initialize();
		$this->vip_prices_model =M("Recharge_package");
	}
	
	function index(){
		$count=$this->vip_prices_model->count();
		$page = $this->page($count, 20);
		$vip_prices=$this->vip_prices_model->where('status = 1')->order('id asc')->limit($page->firstRow . ',' . $page->listRows)->select();		
		$this->assign("Page", $page->show('Admin'));
		$this->assign("vip_prices",$vip_prices);
		$status_name = array("已禁用","已启用");
		$this->assign("status_name",$status_name);
		$this->display();
	}
	
	function add(){
              $this->display();
	}
	
	function add_post(){
		if (IS_POST) {
			$data['money_num'] =I("post.money_num");
			$data['diamond_num'] =I("post.diamond_num");
			$data['status'] =1;
			//自动验证
			$rule = array(
				array('money_num', 'require', '充值金额不能为空！'),
				array('diamond_num', 'require', '获得'.get_balance_name().'不能为空！'),
			);
			 if(!$this->vip_prices_model->field('money_num,diamond_num,status')->validate($rule)->create())
            				$this->error($this->vip_prices_model->getError());
			$result=$this->vip_prices_model->add();
			//echo $this->vip_prices_model->getLastsql();
			if ($result) {
				$this->success("添加成功！");
			} else {
				$this->error("添加失败！");
			}
			
		}
	}
	
	public function edit(){
		$id= intval(I("get.id"));
		$item=$this->vip_prices_model->where("id=$id")->find();
		$this->assign("item",$item);
		$this->display();
	}
	
	public function edit_post(){
		if (IS_POST) {
			$data['money_num'] =I("post.money_num");
			$data['diamond_num'] =I("post.diamond_num");
			$data['id'] =I("post.id");
			//自动验证
			$rule = array(
				array('money_num', 'require', '充值金额不能为空！'),
				array('diamond_num', 'require', '获得'.get_balance_name().'不能为空！'),
				array('id', 'require', 'ID不能为空！'),
			);
			 if(!$this->vip_prices_model->field('money_num,diamond_num,id')->validate($rule)->create())
            				$this->error($this->vip_prices_model->getError());
			$result=$this->vip_prices_model->save();
			//echo $this->vip_prices_model->getLastsql();
			if ($result) {
				$this->success("保存成功！",U('Price/index'));
			} else {
				$this->error("保存失败！");
			}
		}
	}
	
	function delete(){
		$data['status'] = 0;
		if(isset($_POST['ids'])){
			$ids = implode(",", $_POST['ids']);
			if ($this->vip_prices_model->where("id in ($ids)")->save($data)) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}else{
			if(isset($_GET['id'])){
				$id = intval(I("get.id"));
				if ($this->vip_prices_model->where("id = $id")->save($data)) {
					$this->success("删除成功！");
				} else {
					$this->error("删除失败！");
				}
			}
		}
	}	
}