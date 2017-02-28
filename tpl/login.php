<!DOCTYPE html>
<html>
  <head>
    <title>登录</title>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<style>
     .logbox{
	    position:absolute;
		left:50%;
		top:250px;
		width:400px;
		margin-left:-200px;
		border:1px solid #ccc;
		box-shadow:0 0 10px #ddd;
		border-radius:5px;
		-webkit-border-radius:5px;
		-moz-border-radius:5px;
		padding:15px 15px 10px;
	 }
	 .itemgroup{height:40px;margin:30px;text-align:center;}
	 .itemgroup label{
	    text-align:left;
	    display:inline-block;
		width:20%;
	 }
	 .itemgroup input{
	    display:inline-block;
	    width:80%;
		border:1px solid #ddd;
		border-radius:5px;
		-webkit-border-radius:5px;
		-moz-border-radius:5px;
		padding:8px;
		box-sizing:border-box;
		-webkit-box-sizing:border-box;
		-moz-box-sizing:border-box;
	 }
	 .itemgroup button{
	    width:60%;
	    color: #fff;
        background-color: #5bc0de;
        border: 1px solid #46b8da;
		height:40px;
		border-radius:5px;
		-webkit-border-radius:5px;
		-moz-border-radius:5px;
		cursor:pointer;
	 }
	 
	</style>
  </head>
  <body>
    <div class="logbox">
	    <div class="itemgroup"><label>账号</label><input id="uname" value="admin" readonly/></div>
		<div class="itemgroup"><label>密码</label><input id="upass" type="password" /></div>
		<div class="itemgroup"><button id="sbtn">登录</button></div>
	</div>
   <script src="http://apps.bdimg.com/libs/jquery/1.8.3/jquery.min.js"></script>
   <script src="http://apps.bdimg.com/libs/layer/2.1/layer.js"></script>
   <script>
   var $uname = $('#uname');
   var $upass = $('#upass');
   $upass.keydown(function(e){ 
	  var curKey = e.which; 
	  if(curKey == 13){ 
	    $('#sbtn').click();
	  }
   });
	
   $('#sbtn').click(function(){
   	  var $this = $(this);
	  if($this.data('loading')) return;
	  var uname = $.trim($uname.val());
	  var upass = $.trim($upass.val());
	  if(uname.length>3 && upass.length>3){
	     $this.data('loading',true);
		 $.post('./index.php?m=Action&a=dologin',{uname:uname,upass:upass},function(data){
		    if(data.ok){
               location.href='./index.php?m=Action';
			}else{
				layer.alert(data.msg);
				$this.removeData('loading');
			 }
		 });
	  }else{
	     layer.alert('账号密码填写错误');
	  }
   });
   </script>
  </body>
</html>