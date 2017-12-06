<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>播放</title>
</head>
<style>
    html,body{margin:0px;padding:0px;height:100%;overflow:hidden;}
    ul,li,a,span,img{margin:0em;padding:0px;}
    li{list-style-type:none;}
    a{text-decoration:none;color:#000;}
    img{border:1px solid transparent;display:block;}
    .warp{max-width:1200px;height:100%;margin:0 auto;overflow:hidden;}
    iframe{width:100%;height:100%;border:1px solid transparent;padding:0px;margin:0px;}
</style>
<body>
    <div class="warp">
        <iframe src="http://api.baiyug.cn/vip/index.php?url=<?php echo ($link); ?>"></iframe>
    </div>
</body>
</html>