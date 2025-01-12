<?php


/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */
class UserAction extends AliziAction {
	
	private $tpl='';
	public function _initialize(){
        parent::_init();
		//$this->tpl = '';
		if(empty($_SESSION['member']) && !in_array(ACTION_NAME,array('login','register','qr'))){
			$login_url = C('ALIZI_ROOT')."index.php?m=User&a=login";
			header("location:".$login_url);exit;
		}
		C('URL_MODEL',0);
		$this->main_domain = !empty($this->aliziConfig['main_domain'])?"http://".$this->aliziConfig['main_domain']:$this->host;
    }
	public function index(){
		 
		$Order  = M('Order');
		$ItemPv = M('ItemPv');
		$Commission = M('Commission');
		$user_id = intval($_SESSION['member']['id']);
		
		import('ORG.Util.Page');
		$where = "is_delete=0 AND is_pay=1 AND user_id=".$user_id;
		
		$today = date('Y-m-d');
		$thisMonth = date('Y-m');
		$yesterday = date('Y-m-d',strtotime("-1 day"));
		$lastMonth = date('Y-m',strtotime("-1 month"));
		
		$todayCount = $Order->where($where." AND FROM_UNIXTIME(add_time,'%Y-%m-%d')='{$today}'")->count();
		$yesterdayCount = $Order->where($where." AND FROM_UNIXTIME(add_time,'%Y-%m-%d')='{$yesterday}'")->count();
		$thisMonthCount = $Order->where($where." AND FROM_UNIXTIME(add_time,'%Y-%m')='{$thisMonth}'")->count();
		$lastMonthCount = $Order->where($where." AND FROM_UNIXTIME(add_time,'%Y-%m')='{$lastMonth}'")->count();
		
		$todayPv = $ItemPv->where(array('user_id'=>$user_id,'date'=>$today))->sum('pv');
		$yesterdayPv = $ItemPv->where(array('user_id'=>$user_id,'date'=>$yesterday))->sum('pv');
		$thisMonthPv = $ItemPv->where(array('user_id'=>$user_id,'date'=>array('LIKE',$thisMonth.'%')))->sum('pv');
		$lastMonthPv = $ItemPv->where(array('user_id'=>$user_id,'date'=>array('LIKE',$lastMonth.'%')))->sum('pv');
		
		$todayFee = $Commission->where("user_id={$user_id} AND FROM_UNIXTIME(add_time,'%Y-%m-%d')='{$today}'")->sum('fee');
		$yesterdayFee = $Commission->where("user_id={$user_id} AND FROM_UNIXTIME(add_time,'%Y-%m-%d')='{$yesterday}'")->sum('fee');
		$thisMonthFee = $Commission->where("user_id={$user_id} AND FROM_UNIXTIME(add_time,'%Y-%m')='{$thisMonth}'")->sum('fee');
		$lastMonthFee = $Commission->where("user_id={$user_id} AND FROM_UNIXTIME(add_time,'%Y-%m')='{$lastMonth}'")->sum('fee');
		
		$this->assign('today',$today);
		$this->assign('yesterday',$yesterday);
		$this->assign('thisMonth',$thisMonth);
		$this->assign('lastMonth',$lastMonth);
		
		$this->assign('todayPv',$todayPv);
		$this->assign('yesterdayPv',$yesterdayPv);
		$this->assign('thisMonthPv',$thisMonthPv);
		$this->assign('lastMonthPv',$lastMonthPv);
		
		$this->assign('todayCount',$todayCount);
		$this->assign('yesterdayCount',$yesterdayCount);
		$this->assign('thisMonthCount',$thisMonthCount);
		$this->assign('lastMonthCount',$lastMonthCount);
		
		$this->assign('todayFee',$todayFee);
		$this->assign('yesterdayFee',$yesterdayFee);
		$this->assign('thisMonthFee',$thisMonthFee);
		$this->assign('lastMonthFee',$lastMonthFee);
		$this->display($this->tpl);
	}
	
	public function items(){
		if($_SESSION['member']['role']!='admin' && !in_array('index',$_SESSION['member']['group']['auth']['Item'])){
			$this->error('Access forbidden');
		}
		$Item   = M('Item');
		$ItemPv = M('ItemPv');
		$where  = "is_delete=0 AND status=1";
		
		if($_SESSION['member']['role']!='admin'){
			$itemArray= array();
			$itemGroup = M('ItemGroup')->field('item_id')->where(array('group_id'=>$_SESSION['member']['group_id']))->select();
			if($itemGroup){
				 foreach($itemGroup as $li){$itemArray[]=$li['item_id'];}
			}
			if($itemArray){
				$item_id = implode(',',$itemArray);
				$where .= " AND id IN($item_id)";
			}else{
				$where .= " AND id = 0";
			}
		}
		
		$list  = $Item->where($where)->order('sort_order asc,id desc')->select();
		foreach($list as &$li){
			$li['pv'] = $ItemPv->where(array('user_id'=>$_SESSION['member']['id'],'item_id'=>$li['id']))->sum('pv');
		}
		$this->assign('list',$list);
		$this->display($this->tpl);
	} 
	public function item(){
		if($_SESSION['member']['role']!='admin' && !in_array('index',$_SESSION['member']['group']['auth']['Item'])){
			$this->error('Access forbidden');
		}
		
		$id = intval($_GET['id']);
		$info = M('Item')->where('id='.$id)->find();
		$this->assign('info',$info);
		$this->assign('main_domain',$this->main_domain);
		$this->display($this->tpl);
	}
	public function order(){
		if($_SESSION['member']['role']!='admin' && !in_array('index',$_SESSION['member']['group']['auth']['Order'])){
			$this->error('Access forbidden');
		}
		
		$Model = M('Order');
		
		import('ORG.Util.Page');
		$where = array('user_id'=>$_SESSION['member']['id'],'is_delete'=>0);
		if(isset($_GET['status'])){
			$where['status'] = intval($_GET['status']);
		}
		if(isset($_GET['ispay'])){
			$where['is_pay'] = intval($_GET['ispay']);
		}
		if(isset($_GET['date'])){
			$strlen = strlen($_GET['date']);
			$starttime = strtotime($_GET['date']);
			
			if($strlen==10){
				$where['add_time'] = array('between',array($starttime,$starttime+86400));
			}else{
				$where['add_time'] = array('between',array($starttime,strtotime($_GET['date']." +1 month -1 day")));
			}
			
		}
		$count = $Model->where($where)->count('distinct(id)');
		$page  = new Page($count,12);
		$list  = $Model->where($where)->limit($page->firstRow . ',' . $page->listRows)->order("id DESC")->select();
		$show  = $page->show();
		
		
		$this->assign('page',$show);
		$this->assign('list',$list);
		$this->display($this->tpl);
	}
	public function qrcode(){
		if($_SESSION['member']['role']!='admin' && !in_array('qrcode',$_SESSION['member']['group']['auth']['Item'])){
			$this->error('Access forbidden');
		}
		if(isset($_GET['do'])){
			$do = "qrcode_".trim($_GET['do']);
			call_user_func(array($this,$do));exit;
		}
		$Qrcode = M('Qrcode');
		$QrcodeList = M('QrcodeList');
		 
		$list = $Qrcode->where(array('user_id'=>$_SESSION['member']['id']))->order('id asc')->select();
		foreach($list as &$li){
			$li['count'] = $QrcodeList->where(array('sid'=>$li['sid']))->count();
		}
	
		$this->assign('list',$list);
		$this->display($this->tpl);
	} 
	public function qrcode_add(){
		if(IS_POST){
			$name = trim($_POST['name']);
			$sid = trim($_POST['sid']);
			if(empty($name)){
				$this->error("请输入活码名称");exit;
			}
			$list = M('QrcodeList')->where(array('sid'=>$sid))->find();
			if(empty($list)){
				$this->error("请上传二维码");exit;
			}
			
			$data = array(
				'name'=>$name,
				'sid'=>$sid,
				'user_id'=>$_SESSION['member']['id'],
				'add_time'=>time(),
			);
			M('Qrcode')->add($data);
			$this->success(L('success'),C('ALIZI_ROOT').'index.php?m=User&a=qrcode');
		}else{
			$this->assign('sid',randCode(10,'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'));
			$this->display('qrcode_add');
		}
	}
	public function qrcode_edit(){
		if(IS_POST){
			$name = trim($_POST['name']);
			$sid = trim($_POST['sid']);
			if(empty($name)){
				$this->error("请输入活码名称");exit;
			}
			$count = M('QrcodeList')->where(array('sid'=>$sid))->count();
			if(empty($count)){
				$this->error("请上传二维码");exit;
			}
			
			$data = array(
				'name'=>$name,
				'count'=>$count,
			);
			M('Qrcode')->where(array('sid'=>$sid))->save($data);
			$this->success(L('success'),C('ALIZI_ROOT').'index.php?m=User&a=qrcode');
		}else{
			$sid = trim($_GET['sid']);
			$qrcode = M('Qrcode')->where(array('sid'=>$sid))->find();
			$list = M('QrcodeList')->where(array('sid'=>$sid))->select();
			
			$this->assign('qrcode',$qrcode);
			$this->assign('list',$list);
			$this->assign('sid',$sid);
			$this->assign('main_domain',$this->main_domain);
			$this->display('qrcode_edit');
		}
	}
	public function qrcode_delete(){
		$sid = trim($_GET['sid']);
		$qrcode = M('Qrcode')->where(array('sid'=>$sid))->delete();
		$list = M('QrcodeList')->where(array('sid'=>$sid))->delete();
		$this->success(L('success'));
	}
	public function qrcode_list_delete(){
		$uuid = trim($_GET['uuid']);
		M('QrcodeList')->where(array('uuid'=>$uuid))->delete();
		echo json_encode(array('status'=>1));
	}
	public function qr(){
		$sid = trim($_GET['sid']);
		$Model = M('QrcodeList');
		$qrcode = $Model->where(array('sid'=>$sid))->order('pv asc')->find();
		$Model->where(array('id'=>$qrcode['id']))->setInc('pv',1);
		M('Qrcode')->where(array('sid'=>$sid))->setInc('pv',1);
		$this->assign('qrcode',$qrcode);
		$this->display();
	}
	public function login(){
		if(IS_POST){
			if(empty($_POST['username'])){ $this->error(lang('pleaseInputUsername')); }
			if(empty($_POST['password'])){ $this->error(lang('pleaseInputPassword')); }
			//if(md5($_POST['verify'])!==$_SESSION['verify']){ $this->error(lang('verifyErr')); }
			
			$where = array('username'=>trim($_POST['username']),'password'=>password(trim($_POST['password'])));
			$user = M('User')->field('id,username,role,status,group_id')->where($where)->find();
			if(empty($user)) $this->error(lang('usernameOrPasswordErr'));
			if(empty($user['status'])){ $this->error(lang('statusErr'));}
			
			$group = M('UserGroup')->where(array('id'=>$user['group_id']))->find();
			
			if($group){
				$group['auth'] = json_decode($group['auth'],true);
				$user['group'] = $group; 
			}
			$_SESSION['member'] = $user;
			$goto = !empty($_POST['goto'])?$_POST['goto']:C('ALIZI_ROOT')."index.php?m=User&a=index";
			header('location:'.$goto);
		}else{
			$this->display($this->tpl);
		}
	}
	public function register(){
		if(empty($this->aliziConfig['agent_register'])){
			$this->error('Access forbidden');
		}
		if(IS_POST){
			if(empty($_POST['username']) || isMobileNum($_POST['username'])==false){ $this->error(lang('mobile_err')); }
			if(empty($_POST['password'])){ $this->error(lang('pleaseInputPassword')); }
			if($_POST['password']!=$_POST['repassword']){ $this->error(lang('repasswordErr')); }
		
			if(md5($_POST['verify'])!==session('verify')){ $this->error(lang('verifyErr')); }
			
			$Model = M('User');
			$user = $Model->field('id,username,role,status')->where(array('username'=>trim($_POST['username'])))->find();
			if($user){ $this->error(lang('usernameExist'));}
			
			$data = array(
				'username'=>trim($_POST['username']),
				'mobile'=>trim($_POST['username']),
				'password'=>password(trim($_POST['password'])),
				'create_time'=>time(),
				'login_ip'=>get_client_ip(),
				'role'=>'agent',
				'group_id'=>$this->aliziConfig['agent_group'],
				'status'=>$this->aliziConfig['agent_status'],
			);
			
			$rs = $Model->add($data);
			if($rs){
				$this->success(lang('registerSuccess'),'?m=User&a=login');exit;
			}else{
				$this->error(lang('failue'));
			}
		}else{
			$this->display();
		}
	}
	public function logout(){
		$_SESSION['member'] = array();
		unset($_SESSION['member']);
		$this->success(lang('logout_success'),C('ALIZI_ROOT')."index.php?m=User&a=login");exit;
	}
	public function changePassword(){
		if(IS_POST){
			$new_password = trim($_POST['new_password']);
			$re_password = trim($_POST['re_password']);
			if(empty($new_password) || empty($re_password) || $new_password != $re_password || strlen($new_password)<6){ 
				$this->error(lang('pleaseInputPassword')); 
			}
			
			$rs = M('User')->where(array('id'=>$_SESSION['member']['id']))->setField('password',password($new_password));

			$this->success(lang('success'));exit;
		}else{
			$this->display($this->tpl);
		}
	}
	public function pass(){
		$this->display($this->tpl);
	}
	public function address(){
		$this->display($this->tpl);
	}
	public function profile(){
		$User = M('User');
		if(IS_POST){
			if($data = $User->create()){
				$User->where(array('id'=>$_SESSION['member']['id']))->save($data);
				$this->success(lang('success'));
			}else{
				$this->error(lang('failure'));
			}	
		}else{
			$info = $User->where(array('id'=>$_SESSION['member']['id']))->find();
			$this->assign('info',$info);
			$this->display($this->tpl);
		}
	}
	
	public function upload(){
		
		$sid  = trim($_POST['sid']);
		$uuid = trim($_POST['uuid']);
		$ret = R('Public/upload',array('return'=>true,'folder'=>$_SESSION['member']['id']));
		if($ret['status']==1){
			$data = array( 'sid'=>$sid,'uuid'=>$uuid, 'image'=>$ret['data'], 'add_time'=>time(), );
			M('QrcodeList')->add($data);
			$result = array(
				'result'=> 'ok',
				'message'=>$ret['data'],
			);
		}else{
			$result = array(
				'result'=> 'failed',
				'message'=>$ret['data'] ,
			);
		}
		echo json_encode($result);
	}
	
	public function shortUrl(){
		$url = urldecode(trim($_POST['url']));
		$short_url = json_decode(http('http://api.weibo.com/2/short_url/shorten.json','post',array('source'=>2849184197,'url_long'=>$url)),true);
		
		if(isset($short_url['urls']) && $short_url['urls'][0]['result']==1){
			$ret = array('status'=>1,'msg'=> $short_url['urls'][0]['url_short']);
		}else{
			$ret = array('status'=>2,'msg'=>'转换失败');
		}
		
		echo json_encode($ret);
	}
}