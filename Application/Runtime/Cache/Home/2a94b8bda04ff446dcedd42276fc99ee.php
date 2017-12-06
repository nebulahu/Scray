<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>优酷视频解析</title>
</head>
<style>
    html,body{margin:0px;padding:0px;}
    ul,li,a,span,img{margin:0em;padding:0px;}
    li{list-style-type:none;}
    a{text-decoration:none;color:#000;}
    img{border:1px solid transparent;display:block;}
    .content{max-width:1200px;margin:0 auto;overflow:hidden;}
    .video_ul{width:100%;margin: 0 auto;overflow:hidden;}
    .video_li{float:left;margin:10px 20px;width:200px;}
    .video_link{display:block;text-align:center;}
    .video_title{display:block;font-size:16px;margin:5px;}
</style>
<body>
    <div class="content">
        <ul class="video_ul">
            <?php if(is_array($video)): $i = 0; $__LIST__ = $video;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="video_li">
                <a class="video_link" target="_blank" href="<?php echo U('Index/play?link='.base64_encode($vo['videosrc']));?>">
                    <img class="video_img" src="<?php echo ($vo["imgsrc"]); ?>" />
                    <span class="video_title"><?php echo ($vo["title"]); ?></span>
                </a>
            </li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
    </div>
</body>
</html>