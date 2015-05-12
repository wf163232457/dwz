<?php
class ServerAction extends CommonAction {
	public function _filter(&$map)
	{
        if(!empty($_GET['group_id'])) {
            $map['group_id'] =  $_GET['group_id'];
            $this->assign('nodeName','分组');
        }elseif(empty($_POST['search']) && !isset($map['pid']) ) {
			$map['pid']	=	0;
		}
		if($_GET['pid']!=''){
			$map['pid']=$_GET['pid'];
		}
		$_SESSION['currentNodeId']	=	$map['pid'];
		//获取上级节点
		$node  = M("Node");
        if(isset($map['pid'])) {
            if($node->getById($map['pid'])) {
                $this->assign('level',$node->level+1);
                $this->assign('nodeName',$node->name);
            }else {
                $this->assign('level',1);
            }
        }
	}

	/* public function _before_index() {
		$model	=	M("Group");
		$list	=	$model->where('status=1')->getField('id,title');
		$this->assign('groupList',$list);
	} */

	// 获取配置类型
	public function _before_add() {
		$model	=	M("Group");
		$list	=	$model->where('status=1')->select();
		$this->assign('list',$list);
		$node	=	M("Node");
		$node->getById($_SESSION['currentNodeId']);
        $this->assign('pid',$node->id);
		$this->assign('level',$node->level+1);
	}

    public function _before_patch() {
		$model	=	M("Group");
		$list	=	$model->where('status=1')->select();
		$this->assign('list',$list);
		$node	=	M("Node");
		$node->getById($_SESSION['currentNodeId']);
        $this->assign('pid',$node->id);
		$this->assign('level',$node->level+1);
    }
	public function _before_edit() {
		$model	=	M("Group");
		$list	=	$model->where('status=1')->select();
		$this->assign('list',$list);
	}

    /**
     +----------------------------------------------------------
     * 默认排序操作
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function sort()
    {
		$node = M('Node');
        if(!empty($_GET['sortId'])) {
            $map = array();
            $map['status'] = 1;
            $map['id']   = array('in',$_GET['sortId']);
            $sortList   =   $node->where($map)->order('sort asc')->select();
        }else{
            if(!empty($_GET['pid'])) {
                $pid  = $_GET['pid'];
            }else {
                $pid  = $_SESSION['currentNodeId'];
            }
            if($node->getById($pid)) {
                $level   =  $node->level+1;
            }else {
                $level   =  1;
            }
            $this->assign('level',$level);
            $sortList   =   $node->where('status=1 and pid='.$pid.' and level='.$level)->order('sort asc')->select();
        }
        $this->assign("sortList",$sortList);
        $this->display();
        return ;
    }
    
    public function index() {
    	$model	=	M("Server");
		$list	=	$model->select();
		$setting = $list[0];
		$this->assign('setting',$setting);/*  echo '<pre>';print_r($setting);die; */
    	$this->display ();
    	return;
    }
    public function update() {
    	//B('FilterString');
    	$name=$this->getActionName();
    	$model = D ( $name );
    	if (false === $model->create ()) {
    		$this->error ( $model->getError () );
    	}
    	$sql = "update `$name` set ";
    	$i = 0;
    	foreach ($model->data() as $k => $v){
    		if($i > 0) $sql .= ",";
    		$sql .= "`$k` = '$v'";
    		$i++;
    	}///session_start();$_SESSION['data'] = $sql;
    	// 更新数据
    	$list=$model->query ($sql);
    	if (false !== $list) {
    		//成功提示
    		$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
    		$this->success ('编辑成功!');
    	} else {
    		//错误提示
    		$this->error ('编辑失败!');
    	}
    }
}
?>