<?php


/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */

class PublicAction extends Action {
	
	//登陆
    public function login(){
		$username = strFilter(trim($_POST['username']));
		$password = trim($_POST['password']);
		if(empty($username)) $this->error('账号或密码有误');
		$Model = M('User');
		$where = array('username'=>$username ,'password'=> password($password),);
		if($userrole==2) $where['role'] = 'admin';
		$user  = $Model->where($where)->find();
		
		if(!$user) $this->error('账号或密码有误');
		if($user['status']==0) $this->error('用户状态异常，请联系管理员');
		$group_id = $user['group_id'];
		if($user['id']==1){
			$admin_auth = C('ADMIN_AUTH');
			foreach($admin_auth as $k=>$v){
				$admin_auth[$k] = array_keys($v);
			}
			$auth = $admin_auth;
		}else{
			if($user['role']=='member'){ $group_id = $Model->where(array('id'=>$user['pid']))->getField('group_id'); }
			$groupAuth = M('UserGroup')->where(array('id'=>$group_id))->getField('auth');
			$auth = json_decode($groupAuth,true);
		}
		
		$user = array(
			'id'         =>$user['id'],
			'pid'         =>$user['pid'],
			'role'       =>$user['role'],
			'group_id'   =>$group_id,
			'auth'       =>$auth,
			'username'   =>$user['username'],
			'login_time' =>$user['login_time'],
			'login_ip'   =>$user['login_ip'],
		);
		session('user',$user);
		
		$data = array(
			'id'         => $user['id'],
			'login_ip'   => get_client_ip(),
			'login_time' => date('Y-m-d H:i:s'),
		);
		
		R('Alizi/writeLogs',array('logs'=>array('types'=>'Login','username'=>$user['username'],'content'=>'登陆系统')));
		
		$Model->save($data);
		//$url = explode('?url=',$_SERVER['HTTP_REFERER']);
		//$url = count($url)==2?urldecode(urldecode($url[1])):"http://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
		$url = $_SERVER['HTTP_REFERER']?$_SERVER['HTTP_REFERER']:"http://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
		header('location:'.$url);
    }
	
    //退出
	public function logout(){
		$_SESSION['user'] = array();
		unset($_SESSION['user']);	
		$_SESSION = array();
		header('location:http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']);
	}

	public function upload($return=false){

		import("ORG.Util.UploadFile");
		$folder = date('Ym');//用“年-月”来命名图片文件夹名称
        $upload = new UploadFile();
        $upload->maxSize   = 3292200;//设置上传文件大小
        $upload->saveRule  = uniqid; //设置上传文件规则
        $upload->savePath  = './Public/Uploads/'.$folder.'/';//设置附件上传目录
        $upload->allowExts = explode(',', 'jpg,gif,png,jpeg,bmp,txt,doc,docx,xls,xlsx');//设置上传文件类型
		
		$upload->thumbRemoveOrigin = false; //删除原图
        $upload->thumb = false; //设置需要生成缩略图，仅对图像文件有效
        $upload->imageClassPath = 'ORG.Util.Image'; //设置引用图片类库包路径
        $upload->thumbPrefix = ''; //设置需要生成缩略图的文件前缀
        $upload->thumbSuffix = ''; //设置需要生成缩略图的文件后缀
        $upload->thumbPath = ''; //缩略图的保存路径，留空的话取文件上传目录本身
        $upload->subType = 'date'; //子目录创建方式，默认为hash，可以设置为hash或者date
        $upload->thumbMaxWidth = '200';//设置缩略图最大宽度
        $upload->thumbMaxHeight = '200'; //设置缩略图最大高度
		
        if(!$upload->upload()) {
			
        	$info = array('status'=>0,'data'=>$upload->getErrorMsg());
        }else{
            $uploadList = $upload->getUploadFileInfo();//取得成功上传的文件信息
            foreach($uploadList as $k=>$v){
				$_POST['image'][$k] = '/'.$folder.'/'.$v['savename'];
			}
			$info = array('status'=>1,'data'=>$_POST['image'][0]);
        }
        if($return){
        	return $info;
        }else{
        	die(json_encode($info));
        }
	}
	public function keUpload(){
		$image = $this->upload(true);
		echo json_encode(array('error'=>0,'url'=>$image));
	}
	public function items(){
		$items = M('Item')->field('id,sn,name')->select();
		echo json_encode($items);
	}
	//生成html
	public function buildHtml(){
		
		$conf['URL_MODEL']=2;
		$conf['HTML_CACHE_ON']=true;
		$this->setConfig($conf);

		//生成静态需要开启配置HTML_CACHE_ON=true
		$list = M('Tags')->field('alias')->select();
		array_push($list,array('alias'=>'index'));
		foreach($list as $li){
			$url = "http://{$_SERVER['HTTP_HOST']}/".C('ROOT_FILE')."index.php/Index/{$li['alias']}";
			http($url);
		}
		$this->success("静态生成完成！");
	}

	public function setConfig(array $setting){
		$file = '/Public/Common/config.db.php';
		$conf = include('.'.$file);

		$conf = array_merge($conf,$setting);
		$fp = fopen(getcwd().$file, "w+");
		fwrite($fp, "<?php\n return ".var_export($conf,true)."\n?>");
	}

	//清楚缓存
	public function clearCache($print = true){
		delFiles('./Public/Cache/');
		if($print==true)$this->success(lang('缓存清除完成'));
	}
}