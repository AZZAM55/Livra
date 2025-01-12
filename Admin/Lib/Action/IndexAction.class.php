<?php


/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */
class IndexAction extends AliziAction {
	public function _initialize(){
        parent::_init();
    }
    public function index(){
        $Statistics = new StatisticsAction();
        $today = $Statistics->byDate();
        $yesterday = $Statistics->byDate(date('Y-m-d',strtotime('-1 days')));
		
		$date = date('Y-m-d');
		$today_pv  = M('ItemPv')->where("date='{$date}'")->sum('pv');
		
		$date = date('Y-m-d',strtotime('-1 days'));
		$yesterday_pv  = M('ItemPv')->where("date='{$date}'")->sum('pv');
		
        $this->assign('today',$today);
        $this->assign('yesterday',$yesterday);
		$this->assign('today_pv',$today_pv);
        $this->assign('yesterday_pv',$yesterday_pv);
		$this->assign('aliziConfig',S('aliziConfig'));
        $this->display();
    }
	
    //用户信息
    function account(){
            R('User/info',array('id'=>$this->uid));
            $this->display();
    }


    //魔术棒解析扩展工具
    public function __call($name,$arguments) {
        $extend = 'Extend/'.$name;
        R($extend);
        $this->display($extend);
    }

    public function smsBalance(){
    	header('Content-type: application/json');  
    	$aliziConfig = S('aliziConfig');
            if(empty($aliziConfig['sms_account']) || empty($aliziConfig['sms_password'])){
                $json = json_encode(array('status'=>0,'data'=>0));
            }else{
                $data = array(
                    'method'=>'balance',
                    'account'=>$aliziConfig['sms_account'],
                    'password'=>$aliziConfig['sms_password'],
					'url'=>"http://{$_SERVER['SERVER_NAME']}{$_SERVER['SCRIPT_NAME']}",
                );
                $json = http( C('ALIZI_API').'/sms/', 'POST', $data );
            }
    	die($json);
    }
	
}