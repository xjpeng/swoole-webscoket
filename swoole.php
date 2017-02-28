<?php
//实列websocketserver对象
$ws = new swoole_websocket_server("0.0.0.0", 5008);
//配置服务器
//daemonize 后台运行
//task_worker_num 开启task功能
$ws->set(array('daemonize' =>1,'task_worker_num'=>20));

//实列redis并赋值给websocketserver对象当做属性
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

$ws->redis= $redis;


//websocket 主机连接路径为 /appid
//websocket 客户端连接路径为 /appid/uid;
//监听WebSocket连接打开事件
$ws->on('open', function ($ws, $request){
	$path = ltrim($request->server['request_uri'],'/');
	if($path){
		$a = explode('/',$path);
		$appid = $a[0];
		//判断添加主机
		if(count($a)==1){
			//判断主机页面是否已经打开
			if($ws->redis->exists('swoole:master:'.$appid)){
				$ws->push($request->fd, json_encode(array('ok'=>0,'msg'=>'服务已经开启')));
				$ws->close($request->fd);
			}else{
				//添加主机fd到redis
				$ws->redis->set('swoole:master:'.$appid,$request->fd,3600);
			}
		}else{
			
			//判断主机页面是否打开工作
			if(!$ws->redis->exists('swoole:master:'.$appid)){
				$ws->push($request->fd, json_encode(array('ok'=>0,'msg'=>'服务已经停止')));
				$ws->close($request->fd);
			}else{
				//添加客户端到列表
				$ws->redis->zAdd('swoole:client:'.$appid,0,$a[1]);
			    $ws->redis->setTimeout('swoole:client:'.$appid,3600);

				//用户总数统计
			    $cnt = $ws->redis->zCard('swoole:client:'.$appid);
				$masterfd = $ws->redis->get('swoole:master:'.$appid);
				$res = $ws->redis->zRevRangeByScore('swoole:client:'.$appid,10000,0,array('withscores' =>true,'limit'=>array(0,10)));
			    $ws->push($masterfd, json_encode(array('ok'=>1,'cnt'=>$cnt,'res'=>$res,'type'=>'msg')));
			}
		}
	}else{
		$ws->push($request->fd, json_encode(array('ok'=>0,'msg'=>'连接参数错误!')));
		$ws->close($request->fd);
	}
});

//监听WebSocket消息事件
$ws->on('message', function ($ws, $frame){
	 $info = json_decode($frame->data,true);
	 if($info['appid'] && $info['uid']){
		 $uid = $info['uid'];
		 $appid = $info['appid'];
		 $masterfd = $ws->redis->get('swoole:master:'.$appid);
		 if($masterfd){
			 $ws->redis->zIncrBy('swoole:client:'.$appid,1,$uid);
			 $ws->task($appid.'|'.$masterfd);
		 }else{
			 $ws->push($frame->fd, json_encode(array('ok'=>0,'msg'=>'服务已经关闭!')));
			 $ws->close($frame->fd);
		 }
	 }
});

$ws->on('task',function($ws,$taskid,$fromid,$data){
	 list($appid,$masterfd) = explode('|',$data);
	 $key = 'swoole:client:'.$appid;
	 $res = $ws->redis->zRevRangeByScore($key,10000,0,array('withscores' =>true,'limit'=>array(0,10)));
	 $ws->push($masterfd,json_encode(array('ok'=>1,'res'=>$res,'type'=>'list')));
});


//监听WebSocket连接关闭事件
$ws->on('close', function ($ws, $fd){
	$keys = $ws->redis->keys('swoole:master:*');
	if($keys){
		//如果是主机关闭,关闭所有连接
		foreach($keys as $key){
			if($ws->redis->get($key) == $fd){
				$appid = str_replace('swoole:master:','',$key);
				$ws->redis->delete($key);
				$ws->redis->delete('swoole:client:'.$appid);
				break;
			}
		}
	}
});

$ws->on('finish',function($ws,$taskid,$data){
	echo 'send';
});

$ws->start();