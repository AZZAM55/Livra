<?php


/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */
class ItemAction extends AliziAction {
	public function _initialize(){
        parent::_init();
    }
	function index($action=''){
		if(isset($_GET['do'])){
			$method = $_GET['do'];
			$this->$method($_GET['id']);exit;
		}
		
		$Model = M('Item');
		$keyword = isset($_GET['keyword'])?trim($_GET['keyword']):'';
		$category_id = isset($_GET['category_id'])?intval($_GET['category_id']):0;
		$where = "is_delete=0";
		if(!empty($keyword)) $where .= " AND (name LIKE '%$keyword%'  OR sn='{$keyword}')";
		if(!empty($category_id)) $where .= " AND category_id=".$category_id;
		if($this->role!='admin'){
			//$where .= " AND status=1";
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
		$category = M('Category')->where('type=1')->order('sort_order desc,id asc')->select();

		
		$aliziConfig = cache('aliziConfig');
		$ItemPv = M('ItemPv');
		foreach ($list as $key => $value) {
			$list[$key]['pv'] = $ItemPv ->where(array('item_id'=>$value['id']))->sum('pv');
			if($aliziConfig['URL_MODEL']==2){
				$uid = $this->uid==1?'':'-'.$this->uid;
				$url = array(
					'url'=>$this->aliziHost."id/{$value['sn']}.html",
					'order'=>$this->aliziHost."single/{$value['sn']}{$uid}.html",
					'detail'=>$this->aliziHost."detail/{$value['sn']}{$uid}.html",
				);
			}else{
				$uid = $this->uid==1?'':'&uid='.$this->uid;
				$url = array(
					'url'=>$this->aliziHost."index.php?m=Index&a=order&id={$value['sn']}",
					'order'=>$this->aliziHost."index.php?m=Order&id={$value['sn']}{$uid}",
					'detail'=>$this->aliziHost."index.php?m=Order&id={$value['sn']}&tpl=detail{$uid}",
				);
			}
			$list[$key]['url']=$url;
			$list[$key]['paramsCount']= M('ItemParams')->where(array('item_id'=>$value['id']))->count();
		}

        $this->assign('aliziConfig',parent::aliziConfig());
		$this->assign('category',$category);
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->assign('aliziHost',$this->aliziHost);
		$this->display($this->role=='admin'?'index':'itemList');
	}
	
	//内容编辑
	function edit($id=''){

		$where = array('id'=>(int)$id);
		$info  = M('Item')->where($where)->find();
		if($info){
			//$info['params'] = json_decode($info['params'],true);
			$info['params'] = M('ItemParams')->where(array('item_id'=>$info['id']))->order('sort_order asc,id asc')->select();
			$info['send_to'] = json_decode($info['send_to'],true);
			$info['sms_send'] = json_decode($info['sms_send'],true);
			$info['extends'] = json_decode($info['extends'],true);
	
			foreach($info['extends'] as &$extends){
				$extends['value'] = is_array($extends['value'])?$extends['value']:explode('#',$extends['value']);
			}
			
		} 
	
		$category = M('Category')->where('type=1')->order('sort_order desc,id asc')->select();
		$shipping = M('Shipping')->order('id asc')->select();
		
		$this->using($info);

		$this->assign('aliziConfig',parent::aliziConfig());
		$this->assign('shipping',$shipping);
		$this->assign('category',$category);
		$this->assign('info',$info);
		$this->display('edit');
	}
	function copy($id=''){
		$this->edit($id);
	}

	function using($info){
		$id = $info['id'];
		$ItemTemplate = M('ItemTemplate');
		$template  = $ItemTemplate->where(array('id'=>$id))->find();

		$aliziConfig = cache('aliziConfig');
		$options = C('TEMPLATE_OPTIONS');
		//$checked = !empty($template['options'])? json_decode($template['options'],true):json_decode($aliziConfig['order_options'],true);
		$checked = !empty($template['options'])?json_decode($template['options'],true):C('DEFAULT_OPTIONS');
		foreach($options as $k=>$v){
			$options[$k]['checked'] = in_array($k,$checked)?true:false;
		}
		$customColor = $this->getCustomColor($template['template'],'array');
		$this->assign('deaultColor',$customColor);
		$this->assign('url',$this->getUrl($info['sn']));
		$this->assign('info',$info);
		$this->assign('temp',$template);
		$this->assign('options',$options);
		$this->assign('extend',unserialize($template['extend']));
		$this->assign('custom',$this->getCustom());
	}

	function template(){
	
        if(IS_POST){
			if(empty($_POST['id'])) $this->ajaxReturn(0,'操作失败',0);
            //if(empty($_POST['options'])) $this->ajaxReturn(0,'请选择表单选项',0);
            $_POST['options'] = json_encode($_POST['options']);

            $extend = array(
                'padding'=>$_POST['padding'],
                'bottom_nav_list'=>$_POST['bottom_nav_list'],
            );

            $Model = M('ItemTemplate');
		
            $data = $Model->create();
            $data['color'] = json_encode($_POST['color']);
            $data['extend'] = serialize($extend);
            foreach ($data as &$v) { $v=(!get_magic_quotes_gpc())?addslashes($v):$v; }
			
		
		
			$Model->query("REPLACE INTO __TABLE__(`".implode('`,`',array_keys($data))."`) VALUES('".implode("','",array_values($data))."')");
            cache('ItemTemplate'.$data['id'],$data);
            $this->ajaxReturn(null,1,1);
        }
	}
	public function getUrl($id){
		$aliziConfig = cache('aliziConfig');
		if($aliziConfig['URL_MODEL']==2){
			$uid = $this->uid==1?'':'-'.$this->uid;
			$url = array(
				'order'=>$this->aliziHost."single/{$id}{$uid}.html",
				'detail'=>$this->aliziHost."detail/{$id}{$uid}.html",
			);
		}else{
			$uid = $this->uid==1?'':'&uid='.$this->uid;
			$url = array(
				'order'=>$this->aliziHost."index.php?m=Order&id={$id}{$uid}",
				'detail'=>$this->aliziHost."index.php?m=Order&id={$id}&tpl=detail{$uid}",
			);
		}
		$url['buildHtml'] = $this->aliziHost."index.php?m=Order&id={$id}&uid={$this->uid}&tpl=detail&buildHtml=H5";
		return $url;
		
	}


	//分类栏目
	public function category(){
		$action = 'Category/category';
		R($action);
		$this->display($action);
	}
	 
	 public function todo(){
	 	if(IS_POST){
	 		if(isset($_POST['sort'])){
	 			$Model = M('Item');
	 			foreach($_POST['sort_order'] as $k=>$v){ $Model->where(array('id'=>$k))->setField('sort_order',$v); }
	 			$this->success('排序成功');
	 		}else{
	 			parent::deleteAll();
	 		}

	 	}
	 }

	 function getCustom($name=''){
	 	$dir = 'Home/Tpl/Alizi';
	 	$list = scandir($dir);
	 	$dirList = array();
	 	foreach($list as $li){
	 		$customDir = $dir.'/'.$li;
	 		if(is_dir($customDir) && !strstr($li,'.')){
	 			if(file_exists($customDir.'/config.php')){
		 			$config = include($customDir.'/config.php');
		 			//$dirList[$li] = array('id'=>'Alizi/'.$li)+$config;
		 			$dirList[$li] = array_merge(array('id'=>$li),$config);
	 			}
	 		}
	 	}
	 	return empty($name)?$dirList:$dirList[$name];
	 }
	 function getCustomColor($tpl,$format='json'){
	 	$color = C('DEFAULT_COLOR');
	 	if(preg_match('/^Alizi\/(.*)/', $tpl,$map)){
			$tpl = $this->getCustom($map[1]);
			if(!empty($tpl['template_color'])) $color=$tpl['template_color'];
		}
		if($format=='json'){
			$this->ajaxReturn($color,'',1);
		}else{
			return $color;
		}
	 }
	 
	 public function order(){
		$action = 'Order/index';
		R($action);
		$this->display($action,array('id'=>$_GET['id']));
	}
	
	function comments($id){
		
		$Model = M('Comments');
		$where = "1=1 and item_id=".(int)$id;
		import('ORG.Util.Page');
		$count = $Model->where($where)->count();
		$page  = new Page($count,20);
		$list  = $Model->where($where)->limit($page->firstRow . ',' . $page->listRows)->order("id desc")->select();
		$show  = $page->show();
		
		$this->assign('page',$show);
		$this->assign('list',$list);
		$this->display('comments');
	}
	function commentsEdit($item_id=0,$comments_id=0){
		if(!empty($comments_id)){
			$Model = M('Comments');
			$info = $Model->where(array('id'=>$comments_id))->find();
			$this->assign('info',$info);
		}
		$this->display();
	}
	public function commentsImport(){
		if(IS_POST){
			
			$item_id=intval($_POST['item_id']);
			$excel = array();
			$Public = new PublicAction();
			$Model = M('Comments');
			$excelUpload = $Public->upload(true);

			if(empty($excelUpload['status'])){
				$this->ajaxReturn(0,$excelUpload['data'],0);exit;
			}
			
			Vendor("PHPExcel.PHPExcel.IOFactory");
			$objPHPExcel = PHPExcel_IOFactory::load("./Public/Uploads".$excelUpload['data']);
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
			
			foreach($excel as $k=>$v){
				if($k==1){continue;}
				$data = array(
					'item_id'=>$item_id,
					'name'=>$v[0],
					'status'=>1,
					'region'=>$v[1],
					'content'=>$v[2],
					'reply_content'=>$v[3],
					'add_time'=>$v[4],
				);
				$Model->add($data);
				
			}
		
			$this->ajaxReturn(1,lang('action_success'),1);

		}else{
			$status = C('ORDER_STATUS');
			unset($status[0]);
			$this->assign('status',$status);
			$this->assign('delivery',C('DELIVERY'));
			$this->display();
		}
	}
	
	function auth(){
		$ItemGroup = M('ItemGroup');
		if(IS_POST){
			$item = explode(',',$_POST['item_id']);
			foreach($item as $item_id){
				$ItemGroup->where(array('item_id'=>$item_id))->delete();
				if($_POST['group_id']){
					foreach($_POST['group_id'] as $group_id){ $ItemGroup->add(array('item_id'=>$item_id,'group_id'=>$group_id)); }
				}
			}
			$this->ajaxReturn(1,lang('action_success'),1);
		}else{
			$item_id = (int)$_GET['id'];
			$group = $ItemGroup->where(array('item_id'=>$item_id))->field('group_id')->select();
			$check = array();
			if($group){ foreach($group as $li){ $check[] = $li['group_id']; } }
			$list = M('UserGroup')->where(array('role'=>'agent',))->order('id asc')->select();
			$this->assign('check',$check);
			$this->assign('list',$list);
			$this->display();
		}
	}
	
	function qrcode(){
		$Model = M('Item');
		if(IS_POST){
			$data = array(
				'id'=>intval($_POST['id']),
				'redirect_url'=>trim($_POST['redirect_url']),
			);
			$Model->save($data);
			//echo $Model->_sql();
			$this->ajaxReturn(1,lang('action_success'),1);
		}else{
			$id =intval($_GET['id']);
			$info = $Model->where('id='.$id)->find();
			
			$config = parent::aliziConfig();
			if($this->uid==1){
				$uid = array(1=>'',2=>'');
			}else{
				$uid = array( 1=> "&uid=".$this->uid, 2=> '-'.$this->uid,);
			}
			
			$url = array(
				1=>urlencode($this->aliziHost."index.php?m=Order&tpl=detail&id=".$info['sn'].$uid[1]),
				2=>urlencode($this->aliziHost.$config['html_file'].$info['sn'].$uid[2].C('HTML_FILE_SUFFIX')),
			);
	
			//$form = file_get_contents($this->aliziHost."index.php?m=Lite&id=".$info['sn']."&uid=".$this->uid."&pid=".$_SESSION['user']['pid']);
			
			//$this->assign('form',$form);
			$this->assign('info',$info);
			$this->assign('url',$url);
			$this->assign('host',$this->aliziHost);
			$this->display('qrcode',false);
		}
	}
	function editParams(){
		
		$this->display('editParams',false);
		
	}
	 
}