<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class StatisticController extends AdminbaseController {
	protected $users_model;
	function _initialize() {
		parent::_initialize();
		$this->users_model =M("Users");
	}
	//财务报表
	function finance(){
		if(IS_POST){
			if (isset($_POST['start_time']) && !empty($_POST['start_time'])){
				$start_time = $_POST['start_time'];
			}
			if (isset($_POST['end_time']) && !empty($_POST['end_time'])){
				$end_time = $_POST['end_time'];
			}
		}

		if(empty($start_time))
			$start_time =date("Y-m-d", strtotime("-6 day"));

		if(empty($end_time))
			$end_time = date("Y-m-d");

		$sql_end_time =date("Y-m-d", strtotime($end_time)+3600*24);

		if ($_SESSION['ADMIN_ID'] != 1) {
			$where_belong = ' and belong_admin = '.$_SESSION['ADMIN_ID'];
		}

		//充值现金，充值金币
		$orders_model = M("Orders");
		$orderQuery = $orders_model->where("order_status=2 and pay_time >= '$start_time'  and pay_time <'$sql_end_time' $where_belong")->field('sum(pay_amount) AS pay_amount ,sum((balance_after - balance_pre)) AS balance_amount, 0 as play_amount, 0 AS play_success_amount, 0 AS consume_amount, 0 AS exchange_amount, 0 AS spread_amount,DATE(`pay_time`) AS ptime ')->group('ptime')->select(false);

		//抓取次数，抓中次数
		$playQuery = M("DevicePlayLog")->where("status=1 and play_time >= '$start_time' and play_time <= '$sql_end_time' $where_belong")->field("0 AS pay_amount, 0 AS balance_amount,count(id) as play_amount,count(IF(play_result>0,id,null)) as play_success_amount, sum(IF(status=1,price,null)) AS consume_amount, 0 AS exchange_amount, 0 AS spread_amount,DATE(`play_time`) AS ptime")->group('ptime')->select(false);

		//兑换，推广
		$mlogQuery = M("MoneyLog")->where("trans_type > 2 and log_time >= '$start_time' and log_time <= '$sql_end_time' $where_belong")->field("0 AS pay_amount, 0 AS balance_amount,0 as play_amount, 0 AS play_success_amount,0 AS consume_amount, sum(IF(trans_type=3,money,0)) as exchange_amount,sum(IF(trans_type>3,money,0)) as spread_amount,DATE(`log_time`) AS ptime")->group('ptime')->select(false);

		$sql = "select ptime,sum(pay_amount) as pay_amount ,sum(balance_amount) as balance_amount,sum(play_amount) as play_amount,sum(play_success_amount) as play_success_amount, sum(consume_amount) as consume_amount,sum(exchange_amount) as exchange_amount,sum(spread_amount) as spread_amount from ( ($orderQuery)  UNION ALL ($playQuery) UNION ALL ($mlogQuery) ) as a group by ptime order by ptime desc";
		$result = $orders_model->query($sql);

		$total_pay = array_sum(array_column($result, 'pay_amount'));
		$total_balance = array_sum(array_column($result, 'balance_amount'));
		$total_play = array_sum(array_column($result, 'play_amount'));

		$this->assign("result",$result);
		$this->assign("total_pay",$total_pay);
		$this->assign("total_balance",$total_balance);
		$this->assign("total_play",$total_play);

		$this->assign("start_time",$start_time);
		$this->assign("end_time",$end_time);
		$this->display();
	}
	//充值报表
	function payment(){
		if(IS_POST){
			if (isset($_POST['start_time']) && !empty($_POST['start_time'])){
				$start_time = $_POST['start_time'];
			}
			if (isset($_POST['end_time']) && !empty($_POST['end_time'])){
				$end_time = $_POST['end_time'];
			}
		}

		if(empty($start_time))
			$start_time =date("Y-m-d", strtotime("-6 day"));

		if(empty($end_time))
			$end_time = date("Y-m-d");

		$sql_end_time =date("Y-m-d", strtotime($end_time)+3600*24);
		//充值
		$orders_model = M("Orders");
		$orderQuery = $orders_model->where("order_status=2  and pay_way <> 'balance'  and  pay_time >= '$start_time'  and pay_time <'$sql_end_time' ")->field('pay_amount ,pay_way,DATE(`pay_time`) AS ptime ')->select(false);

		$sql = "select ptime, pay_way,sum(pay_amount) as pay_amount_day from ($orderQuery) as a group by ptime,pay_way order by ptime desc ";
		$result = $orders_model->query($sql);

		//充值方式
		$paysetting_model = M("Paysetting");
		$pay_channels = $paysetting_model->field('name ,class_name')->select();
		//var_dump($pay_channels);
		$pay_channels_name['system'] = "系统加款";
		foreach($pay_channels as $x)
			$pay_channels_name[$x['class_name']] = $x['name'];
		//var_dump($pay_channels_name);
		$this->assign("result",$result);
		$this->assign("pay_channels_name",$pay_channels_name);
		$this->assign("start_time",$start_time);
		$this->assign("end_time",$end_time);
		$this->display();
	}

	//消费报表
	function consume(){
		if(IS_POST){
			if (isset($_POST['start_time']) && !empty($_POST['start_time'])){
				$start_time = $_POST['start_time'];
			}
			if (isset($_POST['end_time']) && !empty($_POST['end_time'])){
				$end_time = $_POST['end_time'];
			}
		}

		if(empty($start_time))
			$start_time =date("Y-m-d", strtotime("-6 day"));

		if(empty($end_time))
			$end_time = date("Y-m-d");

		$sql_end_time =date("Y-m-d", strtotime($end_time)+3600*24);
		//消费
		$money_log_model = M("Money_log");
		$consumeQuery = $money_log_model->where("(trans_type=3 or trans_type=17  or trans_type=19 or trans_type=21 or trans_type=22) and log_time >= '$start_time'  and log_time <'$sql_end_time' ")->field('IF(trans_type=3,money,0) AS P3,IF(trans_type=17,money,0) AS P17,IF(trans_type=19,money,0) AS P19,IF(trans_type=21,money,0) AS P21,IF(trans_type=22,money,0) AS P22, DATE(`log_time`) AS ptime')->select(false);

		$sql = "select ptime, sum(P3) as p3_val, sum(P17) as p17_val, sum(P19) as p19_val , sum(P21) as p21_val, sum(P22) as p22_val from ($consumeQuery) as a group by ptime order by ptime desc ";
		$result = $money_log_model->query($sql);

		$total_p3 = array_sum(array_column($result, 'p3_val'));
		$total_p17 = array_sum(array_column($result, 'p17_val'));
		$total_p19 = array_sum(array_column($result, 'p19_val'));
		$total_p21 = array_sum(array_column($result, 'p21_val'));
		$total_p22 = array_sum(array_column($result, 'p22_val'));

		$this->assign("result",$result);

		$this->assign("total_p3",$total_p3);
		$this->assign("total_p17",$total_p17);
		$this->assign("total_p19",$total_p19);
		$this->assign("total_p21",$total_p19);
		$this->assign("total_p22",$total_p22);

		$this->assign("start_time",$start_time);
		$this->assign("end_time",$end_time);
		$this->assign("app_e",get_options("app_e"));
		$this->display();
	}

	//用户报表
	function User(){
		if(IS_POST){
			if (isset($_POST['start_time']) && !empty($_POST['start_time'])){
				$start_time = $_POST['start_time'];
			}
			if (isset($_POST['end_time']) && !empty($_POST['end_time'])){
				$end_time = $_POST['end_time'];
			}
			if (isset($_POST['uid']) && !empty($_POST['uid'])){
				$uid = $_POST['uid'];
			}
		}

		if(!empty($uid))
			$where_and = " and uid = ".intval($uid) ;

		if(empty($start_time))
			$start_time =date("Y-m-d H:i", strtotime("-6 day"));

		if(empty($end_time))
			$end_time = date("Y-m-d H:i", strtotime("+1 day"));

		$sql_end_time =date("Y-m-d", strtotime($end_time)+3600*24);

		if ($_SESSION['ADMIN_ID'] != 1) {
			$where_belong = ' and belong_admin = '.$_SESSION['ADMIN_ID'];
		}



		//充值，普通消费，游戏礼物消费
		$money_log_model = M("Money_log");
		$consumeQuery = $money_log_model->where("log_time >= '$start_time'  and log_time <'$sql_end_time' $where_belong ".$where_and)->field('IF(trans_type=1,money,0) AS P1,IF(trans_type=2,money,0) AS P2,IF(trans_type=3,money,0) AS P3,IF(trans_type=4,money,0) AS P4,IF(trans_type=5,money,0) AS P5, uid')->select(false);

		$sql = "select uid,b.user_nicename ,b.mobile,sum(P1) as p1_val, sum(P2) as p2_val, sum(P3) as p3_val, sum(P4) as p4_val, sum(P5) as p5_val from ($consumeQuery) as a   left join ".C('DB_PREFIX')."users b  on a.uid = b.id group by uid order by p2_val desc limit 0,50";
		//echo $sql;
		$result = $money_log_model->query($sql);

		$this->assign("result",$result);
		$this->assign("start_time",$start_time);
		$this->assign("end_time",$end_time);
		$this->assign("uid",$uid);
		$this->assign("app_e",get_options("app_e"));
		$this->display();
	}

	//用户报表
	function qudao(){
		if(IS_POST){
			if (isset($_POST['start_time']) && !empty($_POST['start_time'])){
				$start_time = $_POST['start_time'];
			}
			if (isset($_POST['end_time']) && !empty($_POST['end_time'])){
				$end_time = $_POST['end_time'];
			}
			if (isset($_POST['uid']) && !empty($_POST['uid'])){
				$uid = $_POST['uid'];
			}
		}

		if(!empty($uid))
			$where_and = " and uid = ".intval($uid) ;

		if(empty($start_time))
			$start_time =date("Y-m-d H:i", strtotime("-6 day"));

		if(empty($end_time))
			$end_time = date("Y-m-d H:i", strtotime("+1 day"));

		$sql_end_time =date("Y-m-d", strtotime($end_time)+3600*24);

		if ($_SESSION['ADMIN_ID'] != 1) {
			$where_belong = ' and belong_admin = '.$_SESSION['ADMIN_ID'];
		}


		$count=$this->users_model->where(array("user_type"=>4))->count();
		$page = $this->page($count, 20);
		$where = array(
			'user_type' => 4,
		);
		if ($_SESSION['ADMIN_ID'] != 1) {
			$where['belong_admin'] = $_SESSION['ADMIN_ID'];
		}

		$result = $this->users_model
		->where($where)
		->order("create_time DESC")
		->limit($page->firstRow . ',' . $page->listRows)
		->select();


		$this->assign("result",$result);
		$this->assign("start_time",$start_time);
		$this->assign("end_time",$end_time);
		$this->assign("uid",$uid);
		$this->assign("app_e",get_options("app_e"));
		$this->display();
	}




	//主播报表
	function Host(){
		if(IS_POST){
			if (isset($_POST['start_time']) && !empty($_POST['start_time'])){
				$start_time = $_POST['start_time'];
			}
			if (isset($_POST['end_time']) && !empty($_POST['end_time'])){
				$end_time = $_POST['end_time'];
			}
			if (isset($_POST['uid']) && !empty($_POST['uid'])){
				$uid = $_POST['uid'];
			}
		}

		if(!empty($uid))
			$where_and = " and uid = ".intval($uid) ;

		if(empty($start_time))
			$start_time =date("Y-m-d", strtotime("-6 day"));

		if(empty($end_time))
			$end_time = date("Y-m-d");

		$sql_end_time =date("Y-m-d", strtotime($end_time)+3600*24);
		//提现4 提现退回5 收礼6  兑换虚拟币13 收费房间收入18  分钟收入20
		$money_log_model = M("Money_log");
		$consumeQuery = $money_log_model->where("(trans_type=4 or trans_type=5 or trans_type=6  or trans_type=13 or trans_type=18 or trans_type=20 or trans_type=23) and log_time >= '$start_time'  and log_time <'$sql_end_time' ".$where_and)->field('IF(trans_type=4,money,0) AS P4,IF(trans_type=5,money,0) AS P5,IF(trans_type=6,money,0) AS P6,IF(trans_type=13,money,0) AS P13,IF(trans_type=18,money,0) AS P18,IF(trans_type=20,money,0) AS P20,IF(trans_type=23,money,0) AS P23, uid')->select(false);

		$sql = "select uid,b.user_nicename ,b.mobile,sum(P4) as p4_val, sum(P5) as p5_val, sum(P6) as p6_val, sum(P13) as p13_val, sum(P18) as p18_val, sum(P20) as p20_val , sum(P23) as p23_val from ($consumeQuery) as a   left join ".C('DB_PREFIX')."users b  on a.uid = b.id group by uid order by p6_val desc limit 0,50";
		//echo $sql;
		$result = $money_log_model->query($sql);

		$this->assign("result",$result);
		$this->assign("start_time",$start_time);
		$this->assign("end_time",$end_time);
		$this->assign("uid",$uid);
		$this->assign("app_e",get_options("app_e"));
		$this->display();
	}

	//游戏礼物统计
	function game_gift(){
		if(IS_POST){
			if (isset($_POST['user_type']) && !empty($_POST['user_type'])){
				$user_type = $_POST['user_type'];
			}
			if (isset($_POST['start_time']) && !empty($_POST['start_time'])){
				$start_time = $_POST['start_time'];
			}
			if (isset($_POST['end_time']) && !empty($_POST['end_time'])){
				$end_time = $_POST['end_time'];
			}
			if (isset($_POST['uid']) && !empty($_POST['uid'])){
				$uid = $_POST['uid'];
			}
		}

		if(empty($user_type))
			$user_type =1 ;

		if(!empty($uid))
			$where_and = " and uid2 = ".intval($uid) ;

		if(empty($start_time))
			$start_time =date("Y-m-d", strtotime("-6 day"));

		if(empty($end_time))
			$end_time = date("Y-m-d");

		$sql_end_time =date("Y-m-d", strtotime($end_time)+3600*24);
		//赠送礼物游戏礼物22  收到游戏礼物23
		$money_log_model = M("Money_log");
		if($user_type ==1){
			$display_string = "指定的玩家共计赠送以下主播礼物数额";
			$display_type = "主播";
			$gamegiftQuery = $money_log_model->where("trans_type=23  and log_time >= '$start_time'  and log_time <'$sql_end_time' ".$where_and)->field('uid,money')->select(false);
		}else{
			$display_string = "指定的主播共计收到如下玩家礼物数额";
			$display_type = "玩家";
			$gamegiftQuery = $money_log_model->where("trans_type=22  and log_time >= '$start_time'  and log_time <'$sql_end_time' ".$where_and)->field('uid,money')->select(false);
		}
		$sql = "select uid,mobile,user_nicename,sum(money) as sum_money from ($gamegiftQuery) as a   left join ".C('DB_PREFIX')."users b  on a.uid = b.id group by uid order by sum_money desc";

		//echo $sql;
		$result = $money_log_model->query($sql);

		$total_money = array_sum(array_column($result, 'sum_money'));
		$this->assign("result",$result);
		$this->assign("display_string",$display_string);
		$this->assign("display_type",$display_type);
		$this->assign("total_money",$total_money);
		$this->assign("user_type",$user_type);
		$this->assign("start_time",$start_time);
		$this->assign("end_time",$end_time);
		$this->assign("uid",$uid);
		$this->display();
	}
}