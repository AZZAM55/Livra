<?php


/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */
class CouponAction extends AliziAction {
	public function _initialize(){
        parent::_init();
    }
	public function index(){

        $Model = M('Coupon');
        import('ORG.Util.Page');

        $kw = trim($_GET['keyword']);
        $types = intval($_GET['types']);
        $is_used = $_GET['is_used'];

        $where = '1=1';
        if(!empty($kw)) $where .= " AND (name = '{$kw}' OR code='{$kw}')";
        if($types>0) $where .= " AND types ={$types}";
        if(is_numeric($is_used)) $where .= " AND is_used ={$is_used}";
		
		if(isset($_GET['aliziExcel'])){
			$options = array(
				'name'=>'名称',
				'code'=>'券号',
				'value'=>'面值',
				'is_used'=>'状态',
				'used_time'=>'使用时间',
			);
			$list  = $Model->where($where)->order('id asc')->select();
			foreach($list as &$li){
				$li['is_used'] = $li['is_used']=='1'?'已使用':'';
				$li['used_time'] = empty($li['used_time'])?'':date('Y-m-d H:i:s',$li['used_time']);
			}
			unset($output[0]);
			parent::aliziExcel($list,$options,date('Y-m-d'));
			exit;
		}else{
			$count = $Model->where($where)->count();
			$page  = new Page($count,50);
			$list  = $Model->where($where)->limit($page->firstRow . ',' . $page->listRows)->order('id desc')->select();
			$show  = $page->show();

			$this->assign('list',$list);
			$this->assign('page',$show);
			$this->display('Coupon:index');
		}
	}
	public function edit(){
		$Model = M('Coupon');
		if(IS_POST){
            $amount = intval($_POST['amount']);
            if($amount<1 || $amount>1000){
                $this->ajaxReturn(0,'数量只能填写1-1000之间的整数',0);
            }
			$prefix = strtoupper(trim($_POST['prefix']));
			$data = array(
                'name'=>trim($_POST['name']),
                'types'=>intval($_POST['types']),
                'value'=>floatval($_POST['value']),
                'start_time'=>time(),
                'expire_time'=>86400*3650,
                'add_time'=>$_SERVER['REQUEST_TIME'],
            );
            for($i=0;$i<$amount;$i++){
                $data['code'] = $prefix.randCode(8);
                $Model->add($data);
            }
			$this->ajaxReturn(1,'操作成功',1);
		}else{
			$id = (int)$_GET['id'];
			if($id){
				$info = $Model->where(array('id'=>$id))->find();
				$this->assign('info',$info);
			}
			$this->display('',false);
		}
	}
}