<!DOCTYPE html>
<html>
  <head>
    <title>个人中心</title>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<style>
     ul{list-style:none;margin:0;padding:0;}
	 li{margin:10px 0}
	 li *{margin-right:25px;}
	 li .delbtn{color:#2d89ef;font-family:Arial;cursor:pointer;}
	 li a{color:#2d89ef;text-decoration:none;}
	 .tip{margin-top:25px;font-size:13px;color:#666;}
	</style>
  </head>
  <body>
  <h3>活动数(<span id="listcnt"><?=count($masterKeys)?></span>/<?=$maxHosts?>)</h3>
  <ul id="mylist">
		<?php foreach($masterKeys as $v): ?>
			<li><span><?=$v?></span><span class="delbtn">x</span><a href="./index.php?m=Action&a=master&token=<?=$v?>" target="_blank">open</a></li>
		 <?php endforeach; ?>
		<?php if(count($masterKeys)<10):?>
        <li><button id="createbtn">添加活动</button></li>
		<?php endif; ?>
  </ul>
  <?php if($timeend) echo '<p class="tip">全部活动将在'.$timeend.'过期,过期后将自动删除.</p>';?>
  <script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
  <script src="http://apps.bdimg.com/libs/layer/2.1/layer.js"></script>
  <script>
     $('ul').on('click','#createbtn',function(){
	     var index = layer.load(1, {shade: [0.2,'#fff']});
		  var $parent = $(this).parent();
		  $.post('./index.php?m=Action&a=createKey')
		  .done(function(data){
		     if(data.ok){
			    var keys = data.keys;
				var len = keys.length;
				if(len){
				   var str='';
				   for(var i=0;i<keys.length;i++){
				      str+='<li><span>'+keys[i]+'</span><span class="delbtn">x</span><a href="./index.php?m=Action&a=master&token='+keys[i]+'" target="_blank">open</a></li>';
				   }
				   if(len<10){
				      str+='<li><button id="createbtn">添加房间</button></li>';
				   }
				  $('#mylist').html(str);
				  $('#listcnt').html(len);
				}
			 }else{
			   layer.alert(data.msg); 
			 }
		  })
		  .fail(function(){
		     layer.alert(data.msg);
		  })
		  .always(function(){
		     layer.close(index);
		  });
	 });
	$('ul').on('click','.delbtn',function(){
	    var $this = $(this);
		layer.confirm('您确定要删除吗？', {
		   btn: ['确定','取消'] //按钮
		}, function(){
		  var id = $this.prev().html();
		  var index = layer.load(1, {shade: [0.2,'#fff']});
		  $.post('./index.php?m=Action&a=delkey',{key:id})
		  .done(function(data){
		     if(data.ok){
                $this.parent().remove();
				var len = $('#listcnt').html()-1;
				$('#listcnt').html(len);
				if(len<10){
				   var $find = $('#mylist').find('#createbtn');
				   if($find.length<1)$('#mylist').append('<li><button id="createbtn">添加房间</button></li>');
				}
				layer.closeAll();
			 }else{
			   layer.alert(data.msg); 
			 }
		  })
		  .fail(function(){
		     layer.alert(data.msg);
		  })
		  .always(function(){
		     layer.close(index);
		  });
		});
	});
  </script>
  </body>
</html>