<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>提示信息</title>
<style type="text/css">
<!--
body{font-family:"Segoe UI","Lucida Grande",Helvetica,Arial,"Microsoft YaHei",FreeSans,Arimo,"Droid Sans","wenquanyi micro hei","Hiragino Sans GB","Hiragino Sans GB W3","FontAwesome",sans-serif;font-size:14px;line-height:24px;color:#666,text-align:center;margin:0;padding:0;background-color:#f9f9f9}a{background-color:transparent;color:#0e90d2;text-decoration:none}a:active,a:hover{outline:0;color:#095f8a}.container{width:520px;margin:100px auto 0;text-align:left;background-color:#FFF;outline:30px solid #FFF}.title{width:500px;padding:5px 10px;background-color:#EEE}.content{width:480px;height:110px;padding:20px;margin-top:10px;color:#C40;background-color:#f8f8f8}
-->
</style>
</head>

<body>
<div class="container">
	<div class="title">提示信息：</div>
	<div class="content"><?php if(isset($message)){ echo $message; } ?></div>
</div>
</body>
</html>