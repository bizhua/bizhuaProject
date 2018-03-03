<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class ShareController extends AdminbaseController {

	//配置
    public function index() {
        $this->assign(get_options("share_options"));
    	$this->display();
    }
    
    //配置处理
    public function index_post() {
    	$_POST = array_map('trim', I('post.'));
    	if(in_array('', $_POST)) $this->error("不能留空！");
       
        $data['option_name']="share_options";
        $data['option_value']=json_encode($_POST);
        if(D("Common/Options")->where("option_name='share_options'")->find()){
            $r=D("Common/Options")->where("option_name='share_options'")->save($data);
        }else{
            $r=D("Common/Options")->add($data);
        }

        if ($r!==false) {
            $this->success("保存成功！");
        } else {
            $this->error("保存失败！");
        }
    }
	
}