<?php


/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */


class OrderAction extends AliziAction {
	public function _initialize(){
        parent::_init();
    }
	function index($order_id=''){
		ini_set('memory_limit', '512M');
		if(isset($_GET['do'])){
			$this->view($_GET['id']);exit;
		}
		$field = "*";//"id,order_no,item_name,quantity,total_price,name,mobile,payment,delivery_name,delivery_no,user_pid,seller_id,remark,add_time";
		$Model = M('Order');
		$Sent = M('Sent');
		$Receive = M('Receive');
		$fields = trim($_GET['fields']);
		$status = $_GET['status'];
		$payment = $_GET['payment'];
		$keyword = trim($_GET['keyword']);
		$category_id = intval($_GET['category_id']);
		$time_start = strtotime($_GET['time_start']);
		$time_end   = strtotime($_GET['time_end']);
		$pageSize = empty($_GET['pageSize'])?25:intval($_GET['pageSize']);
		$where = "is_delete=0";
		
		if(!empty($keyword)) {
			switch($fields){
				case 'channel_id':
					$where .= " AND channel_id ='$keyword'";
				break;
				case 'item_id':
					$where .= " AND item_id ='{$keyword}'";
				break;
				case 'mobile':
					$where .= " AND mobile ='{$keyword}'";
				break;
				case 'address':
					$where .= " AND (address LIKE '%{$keyword}%' OR region LIKE '%{$keyword}%' )";
				break;
				default :$where .= " AND $fields LIKE '%$keyword%' ";
			}
		}
		
		if(is_numeric($status)) $where .= " AND status ={$status} ";
		if(!empty($payment)) $where .= " AND payment LIKE '{$payment}%' ";
		if(!empty($time_start) && $time_start < ($time_end)) $where .= " AND (add_time BETWEEN {$time_start} AND {$time_end})";
		if(!empty($category_id)) $where .= " AND category_id={$category_id}";
		//if(!empty($payment)) $where .= $payment==1? " AND payment =1":" AND payment !=1";//payment=1货到付款，其它为在线支付
		if(!empty($_GET['item_id'])) $where .= " AND item_id ={$_GET['item_id']}";
		if(!empty($_GET['mobile'])) $where .= " AND mobile ='{$_GET['mobile']}'";
		if(!empty($_GET['user_id'])) $where .= " AND user_id ={$_GET['user_id']}";
		if(!empty($_GET['user_pid'])) $where .= " AND user_pid ={$_GET['user_pid']}";
		if(!empty($_GET['update_user_id'])) $where .= " AND update_user_id ={$_GET['update_user_id']}";
		$order = empty($orderby)?"id DESC":"{$orderby} {$sort}";

		switch($this->role){
			case 'admin': 
				$usermap=' AND role="agent"';
			break;
			case 'agent': 
				$usermap= " AND role='member' AND pid={$this->uid}";
				$where .= " AND user_pid ={$this->uid}";
			break;
			case 'member': 
				$usermap=' AND id='.$this->uid;
				$where .= " AND user_id ={$this->uid}";
			break;
		}
		
		$UserModel = M('User');
		$user = $UserModel->where("is_delete=0 ".$usermap)->order('id asc')->select();

		
		if(isset($_GET['aliziExcel']) || $order_id){
			
			if($order_id)$where = array('id'=>array('IN'=>$order_id));
			//header('content-type:text/html;charset=utf8');	
			$aliziConfig = parent::aliziConfig();
			$export = json_decode($aliziConfig['export_order'],true);
			
			$setting = C('setting');
			$options = $setting['export_setting']['export_order']['options'];
			$options = array_intersect_key($options,array_flip($export));
			
			$payments = C('PAYMENT');
			foreach ($payments as $key => $value) { $payment[$key]=$value['name'];}
			$status = C('ORDER_STATUS');//订单状态
			
			
			$list  = $Model->field($field)->where($where)->order('id desc')->select();

		
			if($_GET['fields']=='item_sn' && !empty($list[0]['item_extends']) && $list[0]['item_extends']!='null'){
				$item_extends = json_decode($list[0]['item_extends'],true);
				
				$extends_title = array();
				foreach($item_extends as $k=>$item){
					$item_extends[$k] = is_array($item)?implode(',',$item):$item;
					$extends_title[$k] = $k;
				}
				$options = array_merge($options,$extends_title);
				unset($options['item_extends']);
			}
			$output[] = array_values($options);
			//dump($list);exit;
			foreach($list as &$li){
			
				if(in_array('item_extends',$export)){
					$item_extends = json_decode($li['item_extends'],true);
					$extends = '';
					$extendsArray = array();
					if($item_extends){
						foreach($item_extends as $k=>$v){ 	$extendsArray[] = $k.':'.(is_array($v)?implode('#',$v):$v); }
						$extends = implode("\n",$extendsArray);
					}
					foreach($item_extends as &$item){
						$item = is_array($item)?implode(',',$item):$item;
					}
					$li['item_extends'] = $item_extends;
					if(!empty($item_extends)) $li = array_merge($li,$item_extends);
				}
			
				if(in_array('province',$export)) $li['province'] = preg_replace("/(\d+)\|/","",trim($li['province']));
				if(in_array('city',$export)) $li['city'] = preg_replace("/(\d+)\|/","",trim($li['city']));
				if(in_array('area',$export)) $li['area'] = preg_replace("/(\d+)\|/","",trim($li['area']));
				if(in_array('file',$export)){
					if($li['file']){
						$li['file'] = './Public/Uploads'.$li['file'];
					}
				}
			
				if(in_array('status',$export))$li['status'] = strip_tags($status[$li['status']]);
				if(in_array('payment',$export)){
					$payments = explode('-',$li['payment']);
					$li['payment'] = $payment[$payments[0]];
				}
				if(in_array('item_extends',$export))$li['item_extends'] = $extends;
				if(in_array('add_time',$export))$li['add_time'] = date('Y-m-d H:i:s',$li['add_time']);
				if(in_array('user_pid',$export))$li['user_pid'] = $UserModel->where('id='.$li['user_pid'])->getField('username');
				if(in_array('user_id',$export))$li['user_id'] = $UserModel->where('id='.$li['user_id'])->getField('username');
				if(in_array('update_user_id',$export))$li['update_user_id'] = $UserModel->where('id='.$li['update_user_id'])->getField('username');
				if(in_array('sms',$export))$li['sms'] = M('Receive')->where('order_id='.$li['id'])->getField('receive_content');
				
				if(in_array('address',$export)){
					$li['address'] = $li['address'];
					$li['full_address'] = $li['province']." ".$li['city']." ".$li['area']." ".$li['address'];
					$options['full_address'] = '详细地址';
				}	
				
				if(in_array('admin_remark',$export)){
					$remark = M('OrderLog')->where(array('order_id'=>$li['id'],'remark'=>array('neq','')))->field('remark')->order('id asc')->select();
					$admin_remark = array();
					foreach($remark as $m){$admin_remark[] = $m['remark'];}
					$li['admin_remark'] = implode("\n",$admin_remark);
					$options['admin_remark'] = '管理员备注';
				}	
			
				
			}
			
			if($aliziConfig['export_type']=='csv'){
				$keys = array_keys($options);
				
				foreach($list as $li){
					$newsList = array();
					foreach($keys as $k){ $newsList[$k] = $li[$k]; }
					$output[] = $newsList;
				}
				csv_export($output);exit;
			}else{
				$output = array_merge($output,$list);
				unset($output[0]);
				parent::aliziExcel($output,$options,date('Y-m-d'));exit;
			}
	    }
		
		//$orders = $Model->query("SELECT id,item_id,item_name,name,mobile,count(id) as num FROM __TABLE__ GROUP BY item_id,mobile");

		import('ORG.Util.Page');
		$count = $Model->where($where)->count();
		$page  = new Page($count,$pageSize);
		$list  = $Model->where($where)->field($field)->limit($page->firstRow . ',' . $page->listRows)->order($order)->select();
		
		foreach($list as &$li){
			//是否已发送信息----
			$li['sent'] = array(0=>false,3=>false);
			$li['receive'] = array();
			$send = $Sent->where(array('order_id'=>$li['id']))->select();
			if($send){  foreach($send as $s){  $li['sent'][$s['order_status']] = true; }  }
			
			$li['receive'] = $Receive->where(array('order_id'=>$li['id']))->order('receive_time desc')->select();
			//是否已发送信息----
			
			$li['num'] = $Model->where(array("is_delete"=>0,"item_id"=>$li['item_id'],"mobile"=>$li['mobile']))->count();
			
			$payments = explode('-',$li['payment']);
			$li['payment'] = $payments[0];
		}
		
		$show  = $page->show();
		$category  = M('Category')->order('sort_order desc')->select();
		$status = C('ORDER_STATUS');
		foreach($status as $k=>$v){ 
			$map = "is_delete=0 AND status={$k}";
			if(!empty($payment)) $map .= $payment==1? " AND payment =1":" AND payment !=1";
			switch($this->role){
				case 'agent':  $map .= " AND user_pid ={$this->uid}"; break;
				case 'member':  $map .= " AND user_id ={$this->uid}"; break;
			}
		
			$status[$k] = array('name'=>$v,'count'=>$Model->where($map)->count());
		}

		$this->assign('channel',$channel);
		$this->assign('user',$user);
		$this->assign('category',$category);
		$this->assign('delivery',C('DELIVERY'));
		$this->assign('status',$status);
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display($this->role);
	}

	function cashOnDelivery(){
		$this->index(1);
	}

	function payOnLine(){
		$this->index(2);
	}
	
	//内容编辑
	function view($id=''){

		$Order = M('Order');
		$where = "id={$id}";
		$info  = $Order->where($where)->find();
		if($this->role=='admin' || ($this->role=='agent' && $info['user_pid']==$this->uid) || ($this->role=='member' && $info['user_id']==$this->uid) ){
			if(!empty($info)) $log = M('OrderLog')->where(array('order_id'=>$info['id']))->order('id asc')->select();
			if(isset($_GET['status'])){
				$map = " AND status=".intval($_GET['status']);
			}
			$pre_id = $Order->where("id<{$info['id']}".$map)->order('id desc')->getField('id');
			$next_id = $Order->where("id>{$info['id']}".$map)->getField('id');

			$aliziConfig = cache('aliziConfig');
			$delivery_setting = array_flip(json_decode($aliziConfig['delivery_setting'],true));
			$delivery = array_intersect_key(C('DELIVERY'),$delivery_setting);
			$this->assign('delivery',$delivery);
			$this->assign('info',$info);
			$this->assign('log',$log);
			$this->assign('pre_id',$pre_id);
			$this->assign('next_id',$next_id);
			$this->display($_GET['do']);
		}else{
			$this->error('无权操作');
		}
	}
	

	function updateStatus(){
		
		$id = (int)$_POST['id'];
		$status = (int)$_POST['change_status'];

		$data = array(
			'order_id'=>$id,
			'status'=>$status,
			'user_id'=>(int)$this->uid,
			'remark'=>htmlspecialchars($_POST['action_remark']),
			'delivery_name'=>trim($_POST['delivery_name']),
			'delivery_no'=>trim($_POST['delivery_no']),
		);
		$data['sign'] = createSign($data,C('ALIZI_KEY'));
	
		include("./Home/Lib/Action/ApiAction.class.php");
		R('Api/aliziUpdateStatus',array('data'=>$data));

		//$rs = http($this->aliziHost."index.php?m=Api&a=aliziUpdateStatus", 'POST', array('data'=>$data));

		//$this->ajaxReturn($rs,'操作成功',1);
	}

	//更新快递
	public function deliveryUpdate(){
		$data = array(
			'id'=>(int)$_POST['id'],
			'delivery_name'=>trim($_POST['delivery_name']),
			'delivery_no'=>addslashes($_POST['delivery_no']),
			'update_time'=>time(),
		);
		$rs = M('Order')->save($data);
		if($rs){
			$this->ajaxReturn(null,'保存成功',1);
		}else{
			$this->ajaxReturn(null,'操作失败',0);
		}
		
	}
	public function import(){
		if(IS_POST){
			if($_POST['todo']!='modify'){
				$this->batch($_POST);exit;
			}
			
			$status = intval($_POST['status']);
			$delivery_name = trim($_POST['delivery_name']);
			$time = time();
			$excel = array();
			$Public = new PublicAction();
			$Model = M('Order');
			
			
			if(isset($_POST['upload'])){
				$upfile = $Public->upload(true);
				$excelUpload = $upfile['data'];
				Vendor("PHPExcel.PHPExcel.IOFactory");
				$objPHPExcel = PHPExcel_IOFactory::load("./Public/Uploads".$excelUpload);
				foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
					foreach ($worksheet->getRowIterator() as $row) {
						$cellIterator = $row->getCellIterator();
						$cellIterator->setIterateOnlyExistingCells(false);
						foreach ($cellIterator as $cell) {
							if (!is_null($cell)) {
								//$excel[$cell->getCoordinate()]=$cell->getCalculatedValue();
								$excel[$row->getRowIndex()][]=$cell->getCalculatedValue();
							}
						}
					}
				}
				unset($excel[1]);
				if(count($excel)>0){ $this->ajaxReturn($excel,count($excel),1);}else{$this->ajaxReturn(0,0,0);}
				/*
				foreach($excel as $k=>$v){
					//if($k==1){continue;}
			
					$order_id = $Model->where(array('order_no'=>$v[0]))->getField('id');
			
					if($order_id){
						$data = array('order_id'=>$order_id,'status'=>$status,'remark'=>$v[2],'update_time'=>$time,'user_id'=>$this->uid);
						if($status==3){
							$data['delivery_name']=$delivery_name;
							$data['delivery_no']=$v[1];
						}		

						$data['sign'] = createSign($data,C('ALIZI_KEY'));
						$rs = http($this->aliziHost."index.php?m=Api&a=aliziUpdateStatus", 'POST', array('data'=>$data));
					}
				}
				*/
			}else{
				
				$order_id = $Model->where(array('order_no'=>trim($_POST['order_no'])))->getField('id');
				if($order_id){
					$data = array('order_id'=>$order_id,'status'=>$status,'remark'=>$_POST['remark'],'update_time'=>$time,'user_id'=>$this->uid);
					if($status==3){
						$data['delivery_name']=$delivery_name;
						$data['delivery_no']=$_POST['delivery_no'];
					}		

					$data['sign'] = createSign($data,C('ALIZI_KEY'));
					
					include("./Home/Lib/Action/ApiAction.class.php");
					R('Api/aliziUpdateStatus',array('data'=>$data));
					
					//$rs = http($this->aliziHost."index.php?m=Api&a=aliziUpdateStatus", 'POST', array('data'=>$data));
					//print_r($_POST);print_r($rs);
					$this->ajaxReturn($data,lang('action_success'),1);
				}else{
					$this->ajaxReturn(0,lang('action_失败'),0);
				}
			}
		
			

		}else{
			$status = C('ORDER_STATUS');
			unset($status[0]);
			$this->assign('status',$status);
			$this->assign('delivery',C('DELIVERY'));
			$this->display();
		}
	}
	public function batchImport(){
		if(IS_POST){ 
			$status = intval($_POST['status']);
			$delivery_name = trim($_POST['delivery_name']);
			$time = time();
			$excel = array();
			$Public = new PublicAction();
			$Model = M('Order');
			
			
			if(isset($_POST['upload'])){
				$upfile = $Public->upload(true);
				$excelUpload = $upfile['data'];
				Vendor("PHPExcel.PHPExcel.IOFactory");
				$objPHPExcel = PHPExcel_IOFactory::load("./Public/Uploads".$excelUpload);
				foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
					foreach ($worksheet->getRowIterator() as $row) {
						$cellIterator = $row->getCellIterator();
						$cellIterator->setIterateOnlyExistingCells(false);
						foreach ($cellIterator as $cell) {
							if (!is_null($cell)) {
								//$excel[$cell->getCoordinate()]=$cell->getCalculatedValue();
								$excel[$row->getRowIndex()][]=$cell->getCalculatedValue();
							}
						}
					}
				}
				unset($excel[1]);
				if(count($excel)>0){ $this->ajaxReturn($excel,count($excel),1);}else{$this->ajaxReturn(0,0,0);}
				/*
				foreach($excel as $k=>$v){
					//if($k==1){continue;}
			
					$order_id = $Model->where(array('order_no'=>$v[0]))->getField('id');
			
					if($order_id){
						$data = array('order_id'=>$order_id,'status'=>$status,'remark'=>$v[2],'update_time'=>$time,'user_id'=>$this->uid);
						if($status==3){
							$data['delivery_name']=$delivery_name;
							$data['delivery_no']=$v[1];
						}		

						$data['sign'] = createSign($data,C('ALIZI_KEY'));
						$rs = http($this->aliziHost."index.php?m=Api&a=aliziUpdateStatus", 'POST', array('data'=>$data));
					}
				}
				*/
			}else{
				
				$order_id = $Model->where(array('order_no'=>$_POST['order_no']))->getField('id');
				if($order_id){
					$data = array('order_id'=>$order_id,'status'=>$status,'remark'=>$_POST['remark'],'update_time'=>$time,'user_id'=>$this->uid);
					if($status==3){
						$data['delivery_name']=$delivery_name;
						$data['delivery_no']=$_POST['delivery_no'];
					}		

					$data['sign'] = createSign($data,C('ALIZI_KEY'));
					
					include("./Home/Lib/Action/ApiAction.class.php");
					R('Api/aliziUpdateStatus',array('data'=>$data));
					
					//$rs = http($this->aliziHost."index.php?m=Api&a=aliziUpdateStatus", 'POST', array('data'=>$data));
					//print_r($_POST);print_r($rs);
					$this->ajaxReturn($data,lang('action_success'),1);
				}else{
					$this->ajaxReturn(0,lang('action_失败'),0);
				}
			}
		
			

		}else{
			$status = C('ORDER_STATUS');
			unset($status[0]);
			$this->assign('status',$status);
			$this->assign('delivery',C('DELIVERY'));
			$this->display();
		}
	}
	
	function modify(){
		if(IS_POST){
			$Model = M('Order');
			if($vo=$Model->create()){
				
				if(!empty($_POST['delivery_name_define'])){
					$vo['delivery_name'] = $_POST['delivery_name_define'];
				}
				if(!empty($_POST['item_extends'])){
					$vo['item_extends'] = json_encode($_POST['item_extends']);
				}
				if(isset($_POST['region'])){
					$region = explode(' ',$_POST['region']);
					$vo['province'] = $region[0];
					$vo['city'] = isset($region[1])?$region[1]:'';
					$vo['area'] = isset($region[2])?$region[2]:'';
				}
				if(isset($_POST['update_user_id'])){
					$vo['update_user_id'] = intval($_POST['update_user_id']);
				}
				$vo['update_time'] = time();
	
				$rs = $Model->save($vo);
				//日志记录------------------
				$content= lang('modify_order').',ID:'.$vo['id'];
				$logs=array(
					'types'=>'Order',
					'content'=>$content,
					'username'=>$this->username,
				);
				parent::writeLogs($logs);
				//日志记录------------------
				
				//if($_POST['status']!=$_POST['change_status']){ $this->status();}
				$this->updateStatus();
				
				$this->ajaxReturn(1,lang('modify_success'),1);
				//$this->success(lang('modify_success'),U('Order/index'));
			}else{
				$this->ajaxReturn(0,lang('modify_failue'),0);
				//$this->error(lang('modify_failue'));
			}
		}
	}
	
	public function statistics(){
		R('Statistics/index');
	}
	public function channel(){
		R('Statistics/channel');
	}
	public function region(){
		R('Statistics/byRegion',array('start'=>$_GET['start'],'end'=>$_GET['end']));
	}
	public function time(){
		R('Statistics/byTime',array('start'=>$_GET['start'],'end'=>$_GET['end']));
	}
	public function user(){
		R('Statistics/byUser',array('start'=>$_GET['start'],'end'=>$_GET['end']));
	}
	 
	function add(){
	
		$Model = M('Item');
		$keyword = isset($_GET['keyword'])?trim($_GET['keyword']):'';
		$category_id = isset($_GET['category_id'])?intval($_GET['category_id']):0;
		$where = "is_delete=0";
		if(!empty($keyword)) $where .= " AND (name LIKE '%$keyword%'  OR sn='{$keyword}')";
		if(!empty($category_id)) $where .= " AND category_id=".$category_id;
		if($this->role!='admin'){
			$where .= " AND status=1";
			$itemArray= array();
			$itemGroup = M('ItemGroup')->field('item_id')->where(array('group_id'=>$_SESSION['user']['group_id']))->select();
			if($itemGroup){
				 foreach($itemGroup as $li){$itemArray[]=$li['item_id'];}
			}
			$item_id = implode(',',$itemArray);
			$where .= " AND id IN($item_id)";
		}
		
		$order = "sort_order ASC,id DESC";
		
		import('ORG.Util.Page');
		$count = $Model->where($where)->count('distinct(id)');
		$page  = new Page($count,20);
		$list  = $Model->where($where)->limit($page->firstRow . ',' . $page->listRows)->order($order)->select();
		$show  = $page->show();
	
		
		$aliziConfig = cache('aliziConfig');
		foreach ($list as $key => $value) {
			if($aliziConfig['URL_MODEL']==2){
				$url = array(
					'url'=>$this->aliziHost."id/{$value['sn']}.html",
					'order'=>$this->aliziHost."single/{$value['sn']}-{$this->uid}.html",
					'detail'=>$this->aliziHost."detail/{$value['sn']}-{$this->uid}.html",
				);
			}else{
				$url = array(
					'url'=>$this->aliziHost."index.php?m=Index&a=order&id={$value['sn']}",
					'order'=>$this->aliziHost."index.php?m=Order&id={$value['sn']}&uid={$this->uid}",
					'detail'=>$this->aliziHost."index.php?m=Order&id={$value['sn']}&uid={$this->uid}&tpl=detail",
				);
			}
			$list[$key]['url']=$url;
		}

		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->assign('aliziHost',$this->aliziHost);
		$this->display();
	} 
	
	//批量操作
	public function batch($post=''){
		
		if(IS_POST){
			$post = $post?$post:$_POST;
			$Model = M('Order');
			
			$order_id = $post['order_id'];
			$status = intval($post['status']);
			$delivery_name = trim($post['delivery_name']);
			$update_time = time();
			
			if(empty($order_id)){ $this->ajaxReturn(0,'请选择订单',0); }
			
			
			switch($post['todo']){ 
				case 'down':
					$this->index($order_id);
				break;
				case 'del':
					//批量删除
					$_REQUEST['id'] = $order_id;
					$_REQUEST['do'] = 'del';
					//print_r($_POST);exit;
					parent::proccess('Order');
				break;	
				case 'send0':
					//发送下单通知
					$this->smsSend($order_id,0);
				break;	
				case 'send3':
					//发送货通知
					$this->smsSend($order_id,3);
				break;	
			}
			$this->ajaxReturn($result,lang('action_success'),1);
			
		}else{
			$id = explode(',',trim($_GET['id']));
			$order = M('Order')->where(array('id'=>array('IN',$id)))->field('id,order_no,delivery_no')->select();
			$status = C('ORDER_STATUS');
			unset($status[0]);
			$this->assign('order',$order);
			$this->assign('status',$status);
			$this->assign('delivery',C('DELIVERY'));
			$this->display('batch',false);
		}
	}
	function getReply(){
		$aliziConfig = parent::aliziConfig();
		$data = array(
			'account'=>$aliziConfig['sms_account'],
			'password'=>$aliziConfig['sms_password'],
		);
	
		$rs = http(C('ALIZI_API').'/sms/getReply/','POST',$data);
		$ret = json_decode($rs,true);
		if($ret['status']==1){
			$Order = M('Order');
			$Receive = M('Receive');
			$i = 0;
			foreach($ret['data'] as $data){
				$order_id = $Order->where(array('mobile'=>$data['mobile'],'is_delete'=>0))->order('id desc')->getField('id');
				if($order_id){
					$i++;
					$receiveData = array(
						'order_id'=>$order_id,
						'mobile'=>$data['mobile'],
						'receive_content'=>$data['content'],
						'receive_time'=>$data['reply_time'],
					);
					$Receive->add($receiveData);
				}
			}
			$this->ajaxReturn(null,'成功获取'.$i.'条',1);
		}else{
			$this->ajaxReturn(null,'暂无回复信息',0);
		}
	}
	function smsSend($order_id,$order_status=0){
		
		$orders = M('Order')->where(array('id'=>array('IN',$order_id)))->select();
	
		if($orders){
			foreach($orders as $order){
				$item = M('Item')->where('id='.$order['item_id'])->field('id,sms_send')->find();
				$sms = json_decode($item['sms_send'],true);
				
				if(!empty($sms[$order_status]['content'])){
					$replace = array(
						'#title#'     => $order['item_name'],
						'#params#'     => $order['item_params'],
						'#name#'     => $order['name'],
						'#mobile#'     => $order['mobile'],
						'#quantity#'     => $order['quantity'],
						'#price#'     => $order['total_price'],
						'#express#'     => $express[$order['delivery_name']],
						'#expressNum#'     => $order['delivery_no'],
						'#orderNum#'     => $order['order_no'],
						'#payUrl#'     => shortUrl($this->aliziHost.'Api/pay.php?id='.$order['order_no']),
						'#confirmUrl#'     => $this->aliziHost.(C('URL_MODEL')==2?'a/'.$order['order_no']:'Api/confirm.php?id='.$order['order_no']),
					);
					$id[] = $order['id'];
					$mobile[] = $order['mobile'];
					$content[] = urlencode(str_replace(array_keys($replace),array_values($replace),$sms[$order_status]['content']));
				}
			}
			if(count($orders)==1){
				$method = 'single';
				$mobile = $mobile[0];
				$content = urldecode($content[0]);
			}else{
				$method = 'multi';
				$mobile = implode(',',$mobile);
				$content = implode(',',$content);
			}
			
			$aliziConfig = parent::aliziConfig();
			$data = array(
				'method'=>$method,
				'account'=>$aliziConfig['sms_account'],
				'password'=>$aliziConfig['sms_password'],
				'mobile'=>$mobile,
				'content'=>$content,
			);
		
			$rs = http(C('ALIZI_API').'/sms/send/','POST',$data);
			$ret = json_decode($rs,true);
			$i = 0;
			if(!empty($ret['data'])){
				foreach($ret['data'] as $k=>$li){
					if($li['status']==1){
						$i++;
						$sendData = array(
							'order_id'=>$id[$k],
							'order_status'=>$order_status,
							'mobile'=>$li['mobile'],
							'sent_content'=>$li['content'],
							'sent_time'=>date('Y-m-d H:i:s'),
							'sent_status'=>1,
						);
						M('Sent')->add($sendData);
					}
				}
			}
			
			if($i>0){
				$status = 1;
				$msg = "发送成功{$i}条";
			}else{
				$status = 0;
				$msg = "发送失败";
			}
			$this->ajaxReturn(null,$msg,$status);	
		}else{
			$this->ajaxReturn(null,'请选择订单',0);
		}
		
	}
	
	//物流跟踪
	public function traceExpress(){
		$com = trim($_GET['com']);
		$num = trim($_GET['num']);
		$order_id = intval($_GET['order_id']);
		$order_no = trim($_GET['order_no']);
		$express = C('DELIVERY');
		$json = traceExpress($com,$num,$order_no); 
		$ret  = json_decode($json,true);
		
		$state = array('0'=>'无轨迹','1'=>'已揽收','2'=>'在途中','3'=>'签收','4'=>'问题件',);
		
		
		//更新状态为签收
		if($ret['State']=='3'){
			$data = array(
				'order_id'=>$order_id,
				'status'=>4,
				'user_id'=>(int)$this->uid,
				'remark'=>'接口签收',
			);
			$data['sign'] = createSign($data,C('ALIZI_KEY'));
			include("./Home/Lib/Action/ApiAction.class.php");
			R('Api/aliziUpdateStatus',array('data'=>$data));
		}
		
		$this->assign('list',$ret);
		$this->assign('com',$express[$com]);
		$this->assign('num',$num);
		$this->assign('traceState',$state[$ret['State']]);
		$this->display('traceExpress',false);
	}
}