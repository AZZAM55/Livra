<?php


/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */
class StatisticsAction extends AliziAction {
	public function _initialize(){
        parent::_init();
    }
	function index(){
		$Model = M('Order');
		$start = !empty($_GET['start'])?strtotime($_GET['start']):0;
		$end = !empty($_GET['end'])?strtotime($_GET['end']):time();
		$map = "is_delete=0 AND add_time BETWEEN $start AND $end ";
		
		$kw = trim($_GET['keyword']);
		if(!empty($kw)){ $map .= " AND item_sn='$kw'"; }
		
		
		switch($this->role){
			case 'admin': $map .='';break;
			case 'agent': $map .=' AND user_pid='.$this->uid;break;
			case 'member': $map .=' AND user_id='.$this->uid;break;
		}

		$list = $Model->query("SELECT item_id,item_sn,item_name,sum(total_price) as total_price,count(id) as quantity FROM __TABLE__ WHERE $map GROUP BY item_id ORDER BY quantity DESC");
		$status = C('ORDER_STATUS');
		foreach ($list as $key => $value) {
			foreach($status as $k=>$v){
				$count = $Model->query("SELECT `status`,sum(total_price) as price,count(id) as quantity FROM __TABLE__ WHERE $map AND status=$k AND item_id='{$value['item_id']}'  ");
				$list[$key]['status'][$k] = $count[0];
			}
		}

		$this->assign('list',$list);
		$this->assign('status',$status);
		$this->display('Statistics/index');
	}

	function channel(){
		$Model = M('Order');
		$start = !empty($_GET['start'])?strtotime($_GET['start']):0;
		$end = !empty($_GET['end'])?strtotime($_GET['end']):time();
		$map = "is_delete=0 AND add_time BETWEEN $start AND $end ";
		$kw = trim($_GET['keyword']);
		if(!empty($kw)){ $map .= " AND item_sn='$kw'"; }
		
		switch($this->role){
			case 'admin': $map .='';break;
			case 'agent': $map .=' AND user_pid='.$this->uid;break;
			case 'member': $map .=' AND user_id='.$this->uid;break;
		}
		
		$list = $Model->query("SELECT channel_id,sum(total_price) as total_price,count(id) as quantity FROM __TABLE__ WHERE  $map AND channel_id!='' AND channel_id!='0' GROUP BY channel_id ORDER BY quantity DESC");
		$status = C('ORDER_STATUS');
		foreach ($list as $key => $value) {
			foreach($status as $k=>$v){
				$count = $Model->query("SELECT `status`,sum(total_price) as price,count(id) as quantity FROM __TABLE__ WHERE $map AND status=$k AND channel_id='{$value['channel_id']}' ");
				$list[$key]['status'][$k] = $count[0];
			}
		}
		
		$this->assign('list',$list);
		$this->assign('status',$status);
		$this->display('Statistics/channel');
	}
	public function byDate($date='',$cache=60){
		$date = $date?$date:date('Y-m-d');
		$Order = M('Order');
		$status = C('ORDER_STATUS');
		
		$map = "is_delete=0";
		//$statistics = S('statistics-'.$date);
		//if(empty($statistics)){
			foreach($status as $k=>$v){
				switch($this->role){
					//case 'admin': $map='';break;
					case 'agent': $map .=' AND user_pid='.$this->uid;break;
					case 'member': $map .=' AND user_id='.$this->uid;break;
				}
				//$map = ($this->role=='admin')?"":" AND user_id ={$this->uid}";
				$count = $Order->query("SELECT `status`,sum(total_price) as price,count(id) as quantity FROM __TABLE__ WHERE $map AND status=$k AND FROM_UNIXTIME(add_time,'%Y-%m-%d')='{$date}'");
				$count[0]['name'] = $v;
				$statistics[$k] = $count[0];
			}
			//if($date!=date('Y-m-d')){  S('statistics-'.$date,$statistics,$cache); }
		//}
		return $statistics;
	}
	public function byRegion($start="",$end=""){
		$start = $start?strtotime($start):0;
		$end = ($end?strtotime($end):strtotime(date('Y-m-d')))+86399;
		$Order = M('Order');
		$status = C('ORDER_STATUS');
		
		$map = "is_delete=0";
		$kw = trim($_GET['keyword']);
		if(!empty($kw)){ $map .= " AND item_sn='$kw'"; }
		
		switch($this->role){
			//case 'admin': $map .='';break;
			case 'agent': $map .=' AND user_pid='.$this->uid;break;
			case 'member': $map .=' AND user_id='.$this->uid;break;
		}
		$map .= " AND add_time BETWEEN $start AND $end";
		
		$statistics = $Order->query("SELECT province,sum(total_price) as price,count(id) as quantity FROM __TABLE__ WHERE $map group by province order by quantity desc");

		$this->assign('statistics',$statistics);
		$this->display('Statistics/region');
	}
	public function byTime($start="",$end=""){
		$start = $start?strtotime($start):0;
		$end = ($end?strtotime($end):strtotime(date('Y-m-d')))+86399;
		$Order = M('Order');
		$status = C('ORDER_STATUS');
		
		$map = "is_delete=0";
		$kw = trim($_GET['keyword']);
		if(!empty($kw)){ $map .= " AND item_sn='$kw'"; }
		
		switch($this->role){
			//case 'admin': $map='';break;
			case 'agent': $map .=' AND user_pid='.$this->uid;break;
			case 'member': $map .=' AND user_id='.$this->uid;break;
		}
		$map .= " AND add_time BETWEEN $start AND $end";
		$statistics = $Order->query("SELECT FROM_UNIXTIME(add_time,'%H') as times,sum(total_price) as price,count(id) as quantity FROM __TABLE__ WHERE $map group by times order by times asc");
		
		$this->assign('statistics',$statistics);
		$this->display('Statistics/time');
	}
	
	public function byUser($start="",$end=""){
		$start = $start?strtotime($start):0;
		$end = ($end?strtotime($end):strtotime(date('Y-m-d')))+86399;
		$Order = M('Order');
		$status = C('ORDER_STATUS');
		
		$map = "is_delete=0 and user_id>0 ";
		$kw = trim($_GET['keyword']);
		if(!empty($kw)){ $map .= " AND user_id='$kw'"; }
	
		$map .= " AND add_time BETWEEN $start AND $end";
		$statistics = $Order->query("SELECT user_id,sum(total_price) as price,count(id) as quantity FROM __TABLE__ WHERE  $map group by user_id order by quantity desc");

		$status = C('ORDER_STATUS');
		foreach ($statistics as $key => $value) {
			foreach($status as $k=>$v){
				$count = $Order->query("SELECT `status`,sum(total_price) as price,count(id) as quantity FROM __TABLE__ WHERE is_delete=0 AND user_id={$value['user_id']} AND status=$k AND channel_id='{$value['channel_id']}' ");
				$statistics[$key]['status'][$k] = $count[0];
			}
		}
		
		$this->assign('status',$status);
		$this->assign('statistics',$statistics);
		$this->display('Statistics/user');
	}
	public function byCommission($start="",$end=""){
		$start = $start?strtotime($start):0;
		$end = ($end?strtotime($end):strtotime(date('Y-m-d')))+86399;
		$Commission = M('Commission');
		$status = C('ORDER_STATUS');
		
		$map = "is_delete=0 and user_id>0 ";
		$kw = trim($_GET['keyword']);
		if(!empty($kw)){ $map .= " AND user_id='$kw'"; }
	
		$map .= " AND add_time BETWEEN $start AND $end";
		$statistics = $Commission->query("SELECT user_id,sum(total_price) as price,count(id) as quantity FROM __TABLE__ WHERE  $map group by user_id order by quantity desc");

		$status = C('ORDER_STATUS');
		foreach ($statistics as $key => $value) {
			foreach($status as $k=>$v){
				$count = $Commission->query("SELECT `status`,sum(total_price) as price,count(id) as quantity FROM __TABLE__ WHERE is_delete=0 AND user_id={$value['user_id']} AND status=$k AND channel_id='{$value['channel_id']}' ");
				$statistics[$key]['status'][$k] = $count[0];
			}
		}
		
		$this->assign('status',$status);
		$this->assign('statistics',$statistics);
		$this->display('Statistics/commission');
	}
	 
}