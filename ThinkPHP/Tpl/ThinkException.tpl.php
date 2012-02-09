<?php
    @header("HTTP/1.1 404 Not Found");
    @header("Status: 404 Not Found");

    $admin = true;
    $session_data = Session::get("user_data");

    if($session_data == null || $session_data == "")
    {
        $admin = false;
    }
    else
    {
        import("@.Plugin.XHaffmanSec");

        $encrypt = new XHaffman();
        $session_data = $encrypt->Decode($session_data, C("ENCRYPTION_KEY"));
        $session_array = explode("|", $session_data);

        /** 不是管理员 */
        if($session_array[0] != 3) $admin = false;

        if(time() - $session_array["5"] > 1800)
        {
            Session::set("user_data", "");
            $admin = false;
        }
    }

    if($admin)
    {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>系统发生错误</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
<meta name="Generator" content="EditPlus"/>
<style>
body{
	font-family: 'Microsoft Yahei', Verdana, arial, sans-serif;
	font-size:14px;
}
a{text-decoration:none;color:#174B73;}
a:hover{ text-decoration:none;color:#FF6600;}
h2{
	border-bottom:1px solid #DDD;
	padding:8px 0;
    font-size:25px;
}
.title{
	margin:4px 0;
	color:#F60;
	font-weight:bold;
}
.message,#trace{
	padding:1em;
	border:solid 1px #000;
	margin:10px 0;
	background:#FFD;
	line-height:150%;
}
.message{
	background:#FFD;
	color:#2E2E2E;
		border:1px solid #E0E0E0;
}
#trace{
	background:#E7F7FF;
	border:1px solid #E0E0E0;
	color:#535353;
}
.notice{
    padding:10px;
	margin:5px;
	color:#666;
	background:#FCFCFC;
	border:1px solid #E0E0E0;
}
.red{
	color:red;
	font-weight:bold;
}
</style>
</head>
<body>
<div class="notice">
<h2>系统发生错误 </h2>
<div >您可以选择 [ <A HREF="<?php echo($_SERVER['PHP_SELF'])?>">重试</A> ] [ <A HREF="javascript:history.back()">返回</A> ] 或者 [ <A HREF="<?php echo(__APP__);?>">回到首页</A> ]</div>
<?php if(isset($e['file'])) {?>
<p><strong>错误位置:</strong>　FILE: <span class="red"><?php echo $e['file'] ;?></span>　LINE: <span class="red"><?php echo $e['line'];?></span></p>
<?php }?>
<p class="title">[ 错误信息 ]</p>
<p class="message"><?php echo $e['message'];?></p>
<?php if(isset($e['trace'])) {?>
<p class="title">[ TRACE ]</p>
<p id="trace">
<?php echo nl2br($e['trace']);?>
</p>
<?php }?>
</div>
<div align="center" style="color:#FF3300;margin:5pt;font-family:Verdana"> ThinkPHP <sup style='color:gray;font-size:9pt'><?php echo THINK_VERSION;?></sup><span style='color:silver'> { Fast & Simple OOP PHP Framework } -- [ WE CAN DO IT JUST THINK IT ]</span>
</div>
</body>
</html>
<?php
}
else
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>少年は、失われた</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
<meta name="Generator" content="EditPlus"/>

<style>
body{
	font-family: 'Microsoft Yahei', Verdana, arial, sans-serif;
	background: #4CF;
    margin: 0 0 0 0;
    padding: 0 0 0 0;

    text-shadow: 1px 1px 0px white;
}

#wrapper {
    margin-left: auto;
    margin-right: auto;
    background: #8EE1FF;
    border-radius: 0px 0px 15px 15px;
    -ms-border-radius: 0px 0px 15px 15px;
    -moz-border-radius: 0px 0px 15px 15px;
    -webkit-border-radius: 0px 0px 15px 15px;
    -khtml-border-radius: 0px 0px 15px 15px;
    height: 600px;
    width: 980px;

    -webkit-box-shadow: -2px -2px 2px #23b1ff;
    -moz-box-shadow: -2px -2px 2px #23b1ff;
}

h1 {
    width: 357px;
    height: 65px;

    background: url("<?php echo __ROOT__; ?>/default/images/404_logo.jpg");
    text-indent: -9999em;
}

a:link { color: #666; }
a:visited { color: #666; }
a:hover { color: #444; }

#cse {
    position: relative;
    left: -20px;
}
</style>

    <script type="text/javascript" src="<?php echo __ROOT__; ?>/default/js/jquery-1.6.4.min.js"></script>
    <script type="text/javascript" src="<?php echo __ROOT__; ?>/default/js/fancybox/jquery.mousewheel-3.0.2.pack.js"></script>
    <script type="text/javascript" src="<?php echo __ROOT__; ?>/default/js/fancybox/jquery.fancybox-1.3.1.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo __ROOT__; ?>/default/js/fancybox/jquery.fancybox-1.3.1.css" />

    <script type="text/javascript">
    $(function(){
        $("#togoogle").fancybox({
            "titleShow": false,
            'transitionIn': 'elastic',
            'transitionOut': 'elastic'
        });
    });
    </script>
</head>
<body>
    <div id="wrapper">
        <div width="100%" style="border-bottom: 1px solid #ccc;">
            <div style="height: 529px; float: left;">
                <img style="position: relative; left: -15px;" src="<?php echo __ROOT__; ?>/default/images/404_moe.png" />
            </div>
            <div style="float: left; width: 357px;">
                <h1>页面坏掉了</h1>
                <div>
                    呜咕~ 我“啪啪啪”地坏掉可能是因为：
                    <ul>
                        <li><b style="color: red;">主人 (Developer)</b> 不小心把我写坏掉了</li>
                        <li><b style="color: red;">服务器酱 (Server)</b> 打了个喷嚏开小差了</li>
                        <li><b style="color: red;">骚年 (You)</b> 乃自己兜圈迷路了</li>
                    </ul>
                </div>

                <div style="margin-top: 30px;">
                    接下去，乃可以：
                    <ul>
                        <li><a href="<?php echo __SELF__; ?>">戳戳</a> 我</li>
                        <li><a href="mailto:admin@xcoder.in">呼叫</a> 主人</li>
                        <li><a href="<?php echo __ROOT__; ?>">蹦到</a> 首页</li>
                        <li>
                            <a class="iframe" id="togoogle" href="<?php echo __ROOT__; ?>/default/404google.html?iframe">谷歌</a> 大叔
                        </li>
                    </ul>
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>
        <div style="font-size: 12px; color: #444; text-align: center; padding-top: 5px;">
            <a href="<?php echo __ROOT__; ?>">首页</a> |
            <a href="<?php echo U("OnlineJudge://Problem@"); ?>">题目</a> |
            <a href="<?php echo U("OnlineJudge://Problem/status");; ?>">运行状态</a> |
            <a href="<?php echo U("OnlineJudge://Contest@");; ?>">比赛</a> |
            <a href="<?php echo U("OnlineJudge://User/user_list");; ?>">用户</a> |
                Ningbo University of Technology Online Judge System v1.0
        </div>
    </div>
</body>
</html>
<?php
}
?>