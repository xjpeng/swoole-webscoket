<!DOCTYPE html>
<html>
  <head>
    <title>index</title>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<link href="http://cdn.bootcss.com/layer/2.4/skin/layer.min.css" rel="stylesheet">
	<style>
	.state{text-align:center;font-size:13px;margin-top:30px;}
	.cnt{position:absolute;left:0;top:0;height:50px;line-height:50px;width:100px;background-color:#2d89ef;color:#fff;text-align:center;}
	[v-cloak]{display:none } 
	#result{margin-top:150px;}
	.ttable{height:50px;position:relative;margin-bottom:30px;}
	.ttop{position:absolute;left:0;top:0;height:50px;width:150px;}
	.rank{position:absolute;width:50px;height:50px;text-align:center;font:20px/50px Arial;}
	.rankicon{position:absolute;border:none;width:50px;height:50px;left:60px;}
	.tbody{margin-left:150px;height:50px;position:relative;}
	.tinner{max-width:100%;width:0;height:50px;background-color:#17CA8E}
	.tinnerval{position:absolute;left:0;top:0;height:50px;background-color:rgba(0,0,0,.3);color:#fff;font:20px/50px Arial;padding:0 15px;}
	.title{position:absolute;top:-25px;left:0;}
	#qrcode{position:absolute;right:10px;top:10px;}
	#qrcode::after{
		content:'扫描/点击加入';
		display:block;
		position:absolute;
		top:100%;
		left:0;
		right:0;
		font-size:13px;
		text-align:center;
	}
	</style>
  </head>
  <body>
  <div id="qrcode" data-href="<?=$host?>?m=Action&a=client&token=<?=$sid?>&key=<?=$keys?>"></div>
  <div class="state"></div>
  <div class="cnt"></div>
  <div id="result" v-cloak>
	  <div v-for="value in res" class="ttable">
	  <span class="title">{{$key}}</span>
	     <div class="ttop">
			 <span class="rank">{{$index+1}}</span>
			 <img class="rankicon" v-bind:src="" />
		 </div>
		 <div class="tbody">
		    <div class="tinner" v-bind:style="{width:value*10+'px'}"></div>
			<div class="tinnerval" v-show="value>0">{{value}}</div>
		 </div>
	  </div>
  </div>
  <script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
  <script src="http://apps.bdimg.com/libs/jquery-qrcode/1.0.0/jquery.qrcode.min.js"></script>
  <script src="http://apps.bdimg.com/libs/vue/1.0.14/vue.min.js"></script>
  <script src="http://cdn.bootcss.com/layer/2.4/layer.min.js"></script>
  <script>
	 $("#qrcode").qrcode({ 
		width: 150, 
		height:150, 
		text: "<?=$host?>?m=Action&a=client&token=<?=$sid?>&key=<?=$keys?>"
	}); 
     $('#qrcode').click(function(){
		  var href = $(this).attr('data-href');
		  window.open(href,'_blank');
	 });
     
	var $state = $('.state');
	var $cnt = $('.cnt');
	var vm=new Vue({
	  el: '#result',
	  data:{res:[]}
	})
	
    var wsServer = 'ws://<?=$_SERVER['SERVER_NAME']?>:<?=WEBSOCKET_PORT?>/<?=$sid?>';
	var websocket = new WebSocket(wsServer);
	websocket.onopen = function (evt) {
		 $state.html('连接成功');
		 console.log(evt.data);
	};
	websocket.onclose = function (evt) {
		 $state.html('连接断开');
	};
	websocket.onmessage = function (evt) {
		var data = JSON.parse(evt.data);
		if(data.ok){
	        if(data.type=='msg'){
			   $cnt.html(data.cnt);
			}
		    vm.res = data.res;
		}else{
		    alert(data.msg);
		}
	};
	websocket.onerror = function (evt, e) {
		 $state.html('错误发生: ' + evt.data);
	};
	
  </script>  
  </body>
</html>