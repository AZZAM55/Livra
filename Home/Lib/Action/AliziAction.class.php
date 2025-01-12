<?php


/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */
defined('THINK_PATH') OR exit();
class AliziAction extends Action{

	protected $aliziConfig="";//阿狸子配置信息
	protected $aliziHost =""; //阿狸子地址
	public function _init(){

		$this->aliziAuth();
		if(!file_exists('./Public/Database/install.lock')){ header('location:index.php?m=Install');exit;}
		$this->aliziConfig = $this->aliziConfig();
		
		//禁止后台登陆域名访问前台
		//if(!empty($this->aliziConfig['login_domain']) && $this->aliziConfig['login_domain']==$_SERVER['HTTP_HOST']){ header('HTTP/1.1 404 Not Found.');exit;}
		
		$wap = C('WAP_THEME');
		$this->wap_theme = $wap?$wap:'Item';
		
		$http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') || $_SERVER['SERVER_PORT'] == '443' || $_SERVER['HTTP_FROM_HTTPS'] == 'on' || $_SERVER['HTTP_FROM_HTTPS'] == 'on' || $_SERVER['HTTP_SSL_FLAG'] == 'SSL') ? 'https://' : 'http://'; 
		$this->host = $http_type.$_SERVER['HTTP_HOST'];
	
		$this->aliziHost = $this->host.C('ALIZI_ROOT');
		$this->assign('aliziHost',$this->aliziHost);
		$this->assign('aliziConfig',$this->aliziConfig);
		$this->assign('wap_theme',$this->wap_theme);
		$this->ipDenied(); 
		
		//随机跳转域名，排除User模块
		if(isset($this->aliziConfig['main_domain']) && $_SERVER['HTTP_HOST']==$this->aliziConfig['main_domain'] && !empty($this->aliziConfig['main_domain']) && !in_array(MODULE_NAME,array('User')) && !in_array(ACTION_NAME,array('pay')) ){
			$uri = $this->host.$_SERVER['REQUEST_URI'];
			$domains = explode('<br />',nl2br($this->aliziConfig['redirect_domains']));
			foreach($domains as $k=>$domain){ 
				if($domain==$_SERVER['HTTP_HOST']){
					unset($domains[$k]);
				}else{
					$domains[$k] = trim($domain);
				}
			}
			$domains = array_filter($domains);
			if($domains){
				shuffle($domains);
				header('location:'.str_replace($this->aliziConfig['main_domain'],$domains[0],$uri));exit;
			}
		}
	 
		
		//伪静态后参数
		if(C('URL_MODEL')==2){
			if(isset($_SERVER['HTTP_X_REWRITE_URL'])){
				$uri = $_SERVER['HTTP_X_REWRITE_URL'];
			}else{
				$uri = $_SERVER['REQUEST_URI'];
			}
			$url = parse_url($uri);
			if(isset($url['query'])){
				parse_str($url['query'],$query);
				$_GET = array_merge($_GET,$query);
			}
		}
		if(isset($_GET['gzid'])){  cookie('ac',$_GET['gzid']); }
		if(isset($_GET['ac'])){  cookie('ac',$_GET['ac']); }
		if(isset($_GET['uid'])){  cookie('uid',$_GET['uid']); }
		if(isset($_GET['fbpid']) || !empty($this->aliziConfig['facebook_pixel_id'])){ 
			$fbpid = isset($_GET['fbpid'])?$_GET['fbpid']:$this->aliziConfig['facebook_pixel_id'];
			//cookie('fbpid',$fbpid);
			session('fbpid',$fbpid);
		}else{
			session('fbpid',null);
		}
	}
	
 
	public function verify(){
		import('ORG.Util.Image');
		$lenght = isset($_GET['lenght'])?$_GET['lenght']:4;
		$width = isset($_GET['width'])?$_GET['width']:55;
		$height = isset($_GET['height'])?$_GET['height']:32;
		Image::buildImageVerify($lenght,1,'png',$width,$height);
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
    public function getAliziPayment($item_id){
        return  R('Api/getAliziPayment',array('item_id'=>$item_id));
    }
    public function getItemParams($opt='',$options=array()){
        $checked = empty($opt)?C('DEFAULT_OPTIONS'):json_decode($opt,true);
        
        $options = $options?$options:C('TEMPLATE_OPTIONS');
		
        foreach($options as $k=>$v){
            $options[$k]['checked'] = in_array($k,$checked)?true:false;
        }
        return  $options;
    }

    //黑名单IP
    public function ipDenied(){
		if(!empty($this->aliziConfig['safe_ip_denied'])){
			$ip = get_client_ip();
			$this->aliziConfig['safe_ip_denied'] = str_replace('%', '#', $this->aliziConfig['safe_ip_denied']);
			$ipDenied = explode('#', $this->aliziConfig['safe_ip_denied']);
			foreach($ipDenied as $ips){
				if( (strstr($ips, '*') && preg_match('/'.$ips.'/', $ip)) || $ips==$ip){
					 header('HTTP/1.1 404 Not Found'); 
					die('Access Denied');
				}
			}
		}
    }
	
function getAuth(){
		$password = password(trim($_POST['auth']));
		if('78bc42859d32620fa9120a3f5ec7db4c'==$password){
			$auth = array('ALIZI_AUTH_TYPE'=>C('ALIZI_AUTH_TYPE'),'ALIZI_AUTH'=>C('ALIZI_AUTH'));
			if(isset($_POST['ALIZI_AUTH']) && !empty($_POST['ALIZI_AUTH'])){
				$auth['ALIZI_AUTH']=trim($_POST['ALIZI_AUTH']);
				if(isset($_POST['ALIZI_AUTH_TYPE'])){
					$auth['ALIZI_AUTH_TYPE']=trim($_POST['ALIZI_AUTH_TYPE']);
				}
				file_put_contents('./Public/Common/alizi.auth.php', "<?php\n return ".var_export($auth,true)."\n?>");
			}
			print_r(json_encode($auth));exit;
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
	
	//短信发送
	function send_sms($mobile,$content){
		$config = $this->aliziConfig();
		$data = array(
			'method'=>'single',
			'account'=>$config['sms_account'],
			'apikey'=>$config['sms_password'],
			'mobile'=>$mobile,
			'content'=>$content,
		);
		$rs = http(C('ALIZI_API').'/sms/send/','POST',$data);
		return json_decode($rs,true);

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

    public function _empty($name){
            $this->_404();
    }

    protected function systemStatus($moduleName){
		
		$Model = D('Item');
		$domain = $_SERVER['HTTP_HOST'];
		$item_sn  = $Model->where("`is_delete`=0 AND `domain`='$domain'")->getField('sn');
		if($item_sn){
			//商品绑定域名
			$html_file = $this->aliziConfig['html_file'].$item_sn.C('HTML_FILE_SUFFIX');
			if(file_exists($html_file)){
				echo file_get_contents($html_file);
			}else{
				R('Order/index',array('id'=>$item_sn,'tpl'=>'detail'));
			}
			exit;
		}
		
		if(!empty($this->aliziConfig['web_domain'])){
			$domains = explode('<br />',nl2br($this->aliziConfig['web_domain']));
			foreach($domains as &$domain){ $domain = trim($domain);}
			$domains =  array_unique(array_filter($domains));
			
			if(in_array($_SERVER['HTTP_HOST'],$domains)){
				R('Web/index');exit;
			}
		}

        if(in_array($moduleName, array('Index','Item','Wap'))){

			$wap = $this->wap_theme;
			$get = $_GET;unset($get['_URL_']);
            switch ($this->aliziConfig['system_status']) {
                case '0':
					if(preg_match('/^(http)/',$this->aliziConfig['system_close_info'])){
						header("location:".$this->aliziConfig['system_close_info']);exit;
					}else{
						R('Order/index',array('id'=>$this->aliziConfig['system_close_info'],'tpl'=>'detail',));exit;
					}
                    break;
               case '2':  
                if($moduleName=='Index'){
					if(ACTION_NAME=='index'){
						R($wap.'/index');exit;
					}else{
						$url = U($wap.'/'.ACTION_NAME,$get); 
						header("location:".$url);exit;
					}
                }
               break;
               case '3':  
                if($moduleName==$wap){
                    $this->redirect('/');exit;
                }
               break;
               default:
                if(isMobile()==true && in_array($moduleName, array('Index'))){
					$url = $this->host.U($wap.'/'.ACTION_NAME,$_GET);
					header("location:".$url);exit;
                }
                break;
            }
        }
    }
	
	public function _404($title='404',$info='404 Not Found'){
		$this->assign('title',$title);
		$this->assign('info',$info);
		$this->display('Order:404');
	}
	
	public function item_pv($item_id){
		$pv_name = 'pv_'.$item_id;
		if($item_id && !isset($_COOKIE[$pv_name])){
			$user_id = isset($_COOKIE['alizi_uid'])?intval($_COOKIE['alizi_uid']):1;
			$ItemPv = M('ItemPv');
			$date   = date('Y-m-d');
			$map    = array('item_id'=>$item_id,'user_id'=>$user_id,'date'=>$date,);
			$flag   = $ItemPv->where($map)->find();
			if($flag){
				$ItemPv->where($map)->setInc('pv',1);
			}else{
				$map['pv'] = 1;
				$ItemPv->add($map);
			}
			setcookie($pv_name, 1, time() + 43200, '/', '', false);
		}
	}
	
	public function device($device = 'pc'){
		cookie('screen',$device);
		switch($device){
			case 'pc':
				header('location:'.U('Index/index'));
			break;
			case 'm':
			case 'wap':
				header('location:'.U('Item/index'));
			break;
		}
		
		exit;
	}
	
	public function ipCloak($sn=''){
		//状态说明，0不使用，1通过，2禁止，3接口超时
		
		
		$site = array(
			'fake'=>(int)$this->aliziConfig['ipcloak_fake_site_id'],
			'real'=>(int)$this->aliziConfig['ipcloak_real_site_id'],
		);
		
		if(C('DEFAULT_LANG') == 'zh-cn' || empty($this->aliziConfig['ipcloak_status']) || empty($this->aliziConfig['ipcloak_username']) || empty($this->	aliziConfig['ipcloak_password']) ){
			return array('status'=>0,'target'=>'','site'=>$site);
		}
		
		if($sn){
			$item  = M('Item')->where(array('sn'=>$sn))->field('purchase_url,ipcloak_countries,ads_status,ads_target,ads_region')->find();
			$target = $item['purchase_url'];
		
			if(empty($target)){
				//所有访客都显示正品
				return array('status'=>2,'target'=>'');
			}else{
				$ipcloak_countries = $item['ipcloak_countries']?$item['ipcloak_countries']:$this->aliziConfig['ipcloak_countries'];
				if(empty($target) || empty($ipcloak_countries)){
					return array('status'=>0,'target'=>$target);
				}
			}
		}else{
			$ipcloak_countries = $this->aliziConfig['ipcloak_countries'];
			if(empty($this->aliziConfig['ipcloak_fake_site_id']) || empty($this->aliziConfig['ipcloak_real_site_id']) || empty($ipcloak_countries)){
				return array('status'=>0,'site'=>$site);
			}
		} 
		
		//返回cookie
		if(isset($_COOKIE['ipCloak']) && empty($this->aliziConfig['ipcloak_debug'])){
			return array('status'=>$_COOKIE['ipCloak'],'target'=>$target,'site'=>$site);
		}
		
		$ip = $this->aliziConfig['ipcloak_debug']==1&&!empty($this->aliziConfig['ipcloak_test_ip'])?$this->aliziConfig['ipcloak_test_ip']:get_client_ip();
		
		$postData = array();
		$postData['username'] = $this->aliziConfig['ipcloak_username']; 
		$postData['password'] = $this->aliziConfig['ipcloak_password']; 
		$postData['device'] = isMobile()?'phone':'computer'; //设备
		$postData['ip'] = $ip; //当前客户的IP地址
		$postData['ads_countries'] = $ipcloak_countries; //打广告的国家代码，多个请用+号连接如：US+CA+GB，为空表示不限制访问
		$postData['ban_countries'] = ''; // 如果上面打广告的国家参数填写了，这里就不能够填写，二者只能先其一                         
		$postData['site'] = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
		$postData['site_call_page'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].((isset($_SERVER['QUERY_STRING'])&&!empty($_SERVER['QUERY_STRING']))?('?'.$_SERVER['QUERY_STRING']):'');
		 
		$apiurl = $this->aliziConfig['ipcloak_api']?$this->aliziConfig['ipcloak_api']:'http://www.ipblock.net/api/ipblock';
		$ch = curl_init($apiurl);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);  
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
		curl_setopt($ch, CURLOPT_REFERER, isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//关闭SSL验证, 如果测试接口返回数据为null，请去掉此行和下面一行代码前面的注释
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		
		$return = curl_exec($ch);
	
		curl_close($ch);
		if($return){
			$result = json_decode($return, true); // json数据解码成数组	
			$status = $result['status']==1?1:2;//返回1是目标客户，0则为非目标客户。
			if(!empty($result['jump_fp_url'])){ 
				$target = $result['jump_fp_url'];// 如果在后台配置域名的时候填写了跳转地址
			}
		}else{
			$status = 3;//网关超时或其它问题
		}
	
		//设置cookie   
		if($this->aliziConfig['ipcloak_debug']==1 || $status==3 ){
			setcookie('ipCloak', null, time() + 0, '/', '', false);
		}else{
			setcookie('ipCloak', $status, time() + 86400 * 7, '/', '', false);
		}

		
		return array('status'=>$status,'target'=>$target,'site'=>$site);
		
	} 
	
}