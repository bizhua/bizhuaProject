<?php

/**
 * 会员
 */
namespace User\Controller;
use Common\Controller\AdminbaseController;
class VerifiedadminController extends AdminbaseController {

function index(){
		$where_ands = array();
		$fields=array(
				'status'=> array("field"=>"a.status","operator"=>"="),
				'keyword'  => array("field"=>"b.id","operator"=>"like"),
		);
		if(IS_POST){
			foreach($fields as $param => $val){
				if(isset($_POST[$param]) && !empty($_POST[$param])){
					$operator=$val['operator'];
					$field   =$val['field'];
					$get=$_POST[$param];
					$_GET[$param]=$get;
					if($param == 'status'){
						$field = 'status';
					}
					if($operator=="like"){
						$get="%$get%";
					}
					array_push($where_ands, "$field $operator '$get'");
				}
			}
		}else{
		    foreach($fields as $param => $val){
				if(isset($_GET[$param]) && !empty($_GET[$param])){
					$operator=$val['operator'];
					$field   =$val['field'];
					$get=$_GET[$param];
					$_GET[$param]=$get;
					if($param == 'status'){
						$field = $get;
					}
					if($operator=="like"){
						$get="%$get%";
					}
					array_push($where_ands, "$field $operator '$get'");
				}
			}
		}
		if(empty($where_ands)){
			//$where_ands = array("a.status = 1");
            	//$_GET['status']  = 1;
		}
		$where = join(" and ", $where_ands);
        	$apply_turename_model = M("apply_turename");

		$count = $apply_turename_model->alias("a")->join(C("DB_PREFIX")."users b on b.id = a.uid")->where($where)->count();
		$page = $this->page($count, 15);
        	$field = "a.id as apply_id,a.*,b.*";
		$records = $apply_turename_model->alias("a")->join(C("DB_PREFIX")."users b on b.id = a.uid")->where($where)->field($field)->limit($page->firstRow . ',' . $page->listRows)->order("a.id desc")->select();

		//dump($records);
		$this->assign("formget",$_GET);
		$this->assign("records",$records);
		$status_name = array("<font color='red'>审批拒绝</font>","等待审批","<font color='green'>审批通过</font>");
		$this->assign("status_name",$status_name);
		$this->assign("Page", $page->show('Admin'));
		$this->display(":truename");
    }

    //实名认证审核通过
    function accept(){
        $id= intval(I("get.id"));
        if(empty($id)){
        	$this->error('参数有误');
        }
        $apply_turename_model = M("apply_turename");
        $data['about'] = I("post.about");
        $data['status'] =2;
        $data['verify_time'] = date('Y-m-d H:i:s',time());
        if($apply_turename_model->where("id=$id")->save($data))
        {
            $info=$apply_turename_model ->where("id=$id")->field("uid")->find();
            if($info){
            	$user_model = M("users");
            	$data2['is_truename'] =1;
            	$user_model ->where("id=".$info['uid'])->save($data2);
            	$this->success('审批通过成功');
            }else{
                $this->error('审核失败：未找到该用户信息');
            }
        }else{
            $this->error('请求通过失败');
        }
    }

    //实名认证审核不通过
    function deny(){
        $id= intval(I("get.id"));
        if(empty($id)){
        	$this->error('参数有误');
        }
        $apply_turename_model = M("apply_turename");
        $data['about'] = I("post.about");
        $data['status'] =0;
        $data['verify_time'] = date('Y-m-d H:i:s',time());
        if($apply_turename_model->where("id=$id")->save($data))
        {
            $this->success('拒绝请求成功');
        }else{
            $this->error('拒绝请求失败');
        }
    }
}
