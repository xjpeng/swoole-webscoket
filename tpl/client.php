<!DOCTYPE html>
<html>
  <head>
    <title>index</title>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<style>
	  .state{text-align:center;font-size:13px;margin-top:30px;}
	  .box{text-align:center;margin-top:30px;}
	  .uid{text-align:center;margin-top:40px;font-size:13px;}
	  .btn{width:100px;height:100px;border-radius:10px;border:1px solid #ccc;outline:none;cursor:pointer;}
	  .btn.active{background-color:#fff;}
	  .btn.active:after{content:'点击我';}
	  *{
		-webkit-user-select:none;
		user-select:none;
	 }
	</style>
  </head>
  <body>
     <div class="state"></div>
	 <div class="box">
	   <button class="btn"></button>
	 </div>
	 <p class="uid"><?=$uid?></p>
  <script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
  <script src="http://cdn.bootcss.com/fastclick/1.0.6/fastclick.min.js"></script>
  <script>
	var data= JSON.stringify({appid:'<?=$sid?>',uid:'<?=$uid?>'});
	
	function reloadurl(){
	    var r = Math.random();
	    location.replace('<?=$host?>?m=Action&a=client&token=<?=$sid?>&key=<?=$keys?>&t='+r);
	}
	
	
    var wsServer = 'ws://<?=$_SERVER['SERVER_NAME']?>:<?=WEBSOCKET_PORT?>/<?=$sid?>/<?=$uid?>';
	var websocket = new WebSocket(wsServer);
	var $state = $('.state');
	var $btn = $('.btn');
	
	
	websocket.onopen = function (evt) {
		 $state.html('连接成功');
		 $btn.addClass('active');
	};
	websocket.onclose = function (evt){
		 $state.html('<span style="color:#2d89ef;" onclick="reloadurl();">连接断开</span>');
		 $btn.removeClass('active');
	};
	websocket.onmessage = function (evt) {
		//console.log('Retrieved data from server: ' + evt.data);
	};
	websocket.onerror = function (evt, e) {
		 $state.html('错误发生: ' + evt.data);
	};
	FastClick.attach(document.body);
	$btn.click(function(){
	     var $this = $(this);
		 if($this.hasClass('active')){
		     websocket.send(data);
		 }
	});
  </script>
  </body>
</html>