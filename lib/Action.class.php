<?php
class Action{
	
	private $redis = null;
	
	private $token = 'App1608172';
	
	//最大房间数
	private $maxHosts = 10;
	
	
    public function __construct(){
	     $this->redis = new Redis();
         $this->redis->connect('127.0.0.1', 6379);
		 $this->host = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];
	}
	
    public function index(){
		if(!isset($_SESSION[$this->token])){
			$this->redirect('login');
		}
		$uname = $_SESSION[$this->token];
		$key = $this->token.':'.$uname.':keys';
		if($this->redis->exists($key)){
			$this->masterKeys = $this->redis->sMembers($key);
			$ttl = $this->redis->ttl($key);
			$this->timeend = date('Y-m-d H:i:s',time()+$ttl);
		}else{
			$this->masterKeys=array();
		}
		$this->maxs = $this->maxHosts;
        $this->display();
	}
	
	public function master(){
		if(!isset($_SESSION[$this->token]))$this->redirect('login');
		$uname = $_SESSION[$this->token];
	    $key = $this->token.':'.$uname.':keys';
		$this->keys = base64_encode($key);
		if(!$this->redis->exists($key)) die('活动不存在');
		$this->redis->setTimeout($key,3600);
		$member = $this->_get('token');
		if(!$this->redis->sIsMember($key, $member)) die('参数无效');
		$this->sid = $member;
		$this->display();
	}
	
	public function client(){
		$member = $this->_get('token');
		$key = $this->_get('key');
		if(!$key) die('参数不全!');
		$this->keys = $key;
		$key = base64_decode($key);
		if(!$this->redis->exists($key)) die('活动结束了');
		if(!$this->redis->sIsMember($key, $member))die('参数无效');
		$this->sid = $member;
		//系统分配uid
		$this->uid =str_replace('.','',uniqid('',true));
		$this->display();
	}
	
	
	//创建房间
	public function createKey(){
		if(!$this->isPost()) $this->ajaxReturn(array('ok'=>false,'msg'=>'非法操作!'));
		if(!isset($_SESSION[$this->token])) $this->ajaxReturn(array('ok'=>false,'msg'=>'非法操作!'));
		$uname = $_SESSION[$this->token];
		$key = $this->token.':'.$uname.':keys';
		if($this->redis->exists($key)){
			if($this->redis->sCard($key)>=$this->maxHosts){
				$this->ajaxReturn(array('ok'=>false,'msg'=>'超过房间数限制!'));
			}
		}
		list($usec, $sec) = explode(" ", microtime());
		$mt = md5($sec.ltrim($usec,'0.'));
		$this->redis->sAdd($key,$mt);
		$this->redis->setTimeout($key,3600);
		$keys = $this->redis->sMembers($key);
		$this->ajaxReturn(array('ok'=>true,'keys'=>$keys));
   }
   
   
   //AJAX删除键值
   public function delkey(){
	   if($this->isPost()){
			if(isset($_SESSION[$this->token])){
				$id = $this->_post('key');
				$uname = $_SESSION[$this->token];
				$key = $this->token.':'.$uname.':keys';
				if($this->redis->exists($key)){
					$this->redis->sRem($key,$id);
				}
				$this->ajaxReturn(array('ok'=>true));
			}else{
				$this->ajaxReturn(array('ok'=>false,'msg'=>'非法操作!'));
			}
		}else{
			$this->ajaxReturn(array('ok'=>false,'msg'=>'非法操作!'));
		}
   }
   
   	public function login(){
		if(isset($_SESSION[$this->token])){
			$this->redirect('index');
		}
		$this->display();
	}
	
    public function dologin(){
       $uname = strtolower($this->_post('uname'));
	   $upass = $this->_post('upass');
	   $users=array('admin'=>'666666');
	   if(array_key_exists($uname, $users)){
		   if($upass==$users[$uname]){
			   $_SESSION[$this->token] = $uname;
               $this->ajaxReturn(array('ok'=>true)); 
		   }else{
			   $this->ajaxReturn(array('ok'=>false,'msg'=>'账号或者密码错误')); 
		   }
		   
	   }else{
		   $this->ajaxReturn(array('ok'=>false,'msg'=>'账号或者密码错误')); 
	   }
    }
   
    protected function _post($v){
		return filter_input(INPUT_POST,$v);
	}
	
	protected function _get($v,$default=null){
		$v = filter_input(INPUT_GET,$v);
		if(is_null($v)) return $default;
		return $v;
	}
	
    protected function ajaxReturn($data){
	   header('Content-type: application/json');
	   echo json_encode($data);
	   exit;
    }
	
	protected function redirect($a){
		$action = get_class($this);
		$url = './index.php?m='.$action.'&a='.$a;
		header("Location:{$url}");
	}
	
	protected function isPost(){
		return $_SERVER['REQUEST_METHOD']==='POST';
	}
	
	
	protected function display($template= null){
		if(!$template) $template = $this->_get('a','index');
		extract(get_object_vars($this));
		ob_clean();
		require TPL_PATH.$template.'.php';   
		$view = ob_get_clean();
		echo $view;
	}
	
}
?>