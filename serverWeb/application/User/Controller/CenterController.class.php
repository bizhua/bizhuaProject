<?php

/**
 * 会员中心
 */
namespace User\Controller;
use Common\Controller\MemberbaseController;
class CenterController extends MemberbaseController {

    protected $users_model;
    function _initialize(){
        if(empty($_SESSION['family'])){ //未登录时直接跳登录
            $this->display(":login");die();
        }
    	$this->users_model=D("Common/Users");
    }
    //会员中心
    public function index() {
    	$userid=sp_get_current_userid();
    	$user=$this->users_model->where(array("id"=>$userid))->find();
    	$this->assign($user);
    	           $this->display(':fcenter');
           }

   public function findex(){
        if(IS_POST){
            if (isset($_POST['start_time']) && !empty($_POST['start_time'])){
                $start_time = $_POST['start_time'];
                $_GET['start_time'] = $_POST['start_time'];
            }
            if (isset($_POST['end_time']) && !empty($_POST['end_time'])){
                $end_time = $_POST['end_time'];
                $_GET['end_time'] = $_POST['end_time'];
            }
        }else{
            if (isset($_GET['start_time']) && !empty($_GET['start_time'])){
                $start_time = $_GET['start_time'];
            }
            if (isset($_GET['end_time']) && !empty($_GET['end_time'])){
                $end_time = $_GET['end_time'];
            }
        }


        if(empty($start_time)){
            $start_time =date("Y-m-d", strtotime("-6 day"));
            $_GET['start_time'] = $start_time;
            $start_time = $start_time." 00:00";
        }

        if(empty($end_time)){
            $end_time = date("Y-m-d");
            $_GET['end_time'] = $end_time;
            $end_time = $end_time." 23:59";
        }

        $users_model=M("Users");
        $where = $where."belong_qudao = ".$_SESSION["family"]["id"]." and create_time <= '$end_time' and create_time >= '$start_time'";
        
        $count=$users_model->where($where)->count();
        $page = $this->page($count, 15);

        $user_list = $users_model->where($where)->limit($page->firstRow . ',' . $page->listRows)->order('id DESC')->select();

        $this->assign('user_list',$user_list);

        $this->assign("start_time",$_GET['start_time']);
        $this->assign("end_time",$_GET['end_time']);
        $this->assign("all_user_num",$count);

        $this->assign("page", $page->show('default'));
        $this->display(':fcenter');
    }

    function logout(){
        session("family",null);//只有家族长用户退出
        redirect(__ROOT__."/user/login/index");
    }

    //充值记录
    public function fwithdraw() {
        if(IS_POST){
            if (isset($_POST['start_time']) && !empty($_POST['start_time'])){
                $start_time = $_POST['start_time'];
                $_GET['start_time'] = $_POST['start_time'];
            }
            if (isset($_POST['end_time']) && !empty($_POST['end_time'])){
                $end_time = $_POST['end_time'];
                $_GET['end_time'] = $_POST['end_time'];
            }
        }else{
            if (isset($_GET['start_time']) && !empty($_GET['start_time'])){
                $start_time = $_GET['start_time'];
            }
            if (isset($_GET['end_time']) && !empty($_GET['end_time'])){
                $end_time = $_GET['end_time'];
            }
        }


        if(empty($start_time)){
            $start_time =date("Y-m-d", strtotime("-6 day"));
            $_GET['start_time'] = $start_time;
            $start_time = $start_time." 00:00";
        }

        if(empty($end_time)){
            $end_time = date("Y-m-d");
            $_GET['end_time'] = $end_time;
            $end_time = $end_time." 23:59";
        }

        $users_model=M("Users");
        $where = "a.belong_qudao = ".$_SESSION["family"]["id"]." and order_time <= '$end_time' and order_time >= '$start_time' and item_type = 'packet' and order_status = 2";

        $join = "".C('DB_PREFIX').'users as b on b.id =a.uid';

        $count=M('Orders')->alias('a')->join($join)->field('uid,pay_amount,order_time,user_nicename,avatar')->where($where)->count();
        $page = $this->page($count, 15);

        $result=M('Orders')->alias('a')->join($join)->field('uid,pay_amount,order_time,user_nicename,avatar')->where($where)->limit($page->firstRow . ',' . $page->listRows)->order('a.id DESC')->select();
        $all_money=M('Orders')->alias('a')->join($join)->field('sum(pay_amount) as money')->where($where)->find();
        if (empty($all_money['money'])) {
           $all_money = 0;
        }else{
           $all_money = $all_money['money'];
        }



        $this->assign("result",$result);
        $this->assign("start_time",$_GET['start_time']);
        $this->assign("end_time",$_GET['end_time']);
        $this->assign("all_money",$all_money);
        $this->assign("page", $page->show('default'));
        $this->display(':fwithdraw');
    }
}
