<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title><?php echo ($webname); ?></title>
    <script type="text/javascript" src="__ROOT__/js/jquery-1.6.4.min.js"></script>
    <script type="text/javascript" src="__ROOT__/js/jquery.backstretch.min.js"></script>
    <script type="text/javascript">
        function login() {
            $("#tip-panel span").html('<img src="__ROOT__/images/loading.gif" alt="Loading"> Waiting...');
            $("#tip-panel").slideDown("normal");

            var username = $("input[name=username]").val();
            var password = $("input[name=password]").val();
            var token = $("input[name=<?php echo C('TOKEN_NAME');?>]").val();

            $.post("<?php echo U('chklogin');?>", { "username": username, "password": password, "<?php echo C('TOKEN_NAME');?>": token }, function(html){
                if("1" != html)
                {
                    $("#tip-panel span").html(html);
                }
                else
                {
                    $("#tip-panel span").html('<img src="__ROOT__/images/loading.gif" alt="Loading"> Successfully! Now redirecting...');
                    window.location.href = "<?php echo U('Admin-Do/index');?>";
                }
            });
        }

        $(function() {
            $.backstretch("__ROOT__/images/admin/miku.jpg");

            $("#login-btn").click(function(){
                login();
            });
            
            $("#login-table input").keypress(function(e){
                if(e.keyCode == 13) login();
            });
        });
    </script>

    <style type="text/css">
        body {
            margin: 0 0 0 0;
            padding: 0 0 0 0;
        }
        .fl { float: left; }
        .fr { float: right; }
        .cl { clear: both; }
        #think_page_trace {
            display: none;
        }
        #login-panel {
            width: 400px;
            height: 220px;

            margin-top: 200px;
            margin-left: auto;
            margin-right: auto;

            background: #000;

            border: 1px solid #fff;
            border-radius: 15px 15px 15px 15px;
            -ms-border-radius: 15px 15px 15px 15px;
            -moz-border-radius: 15px 15px 15px 15px;
            -webkit-border-radius: 15px 15px 15px 15px;
            -khtml-border-radius: 15px 15px 15px 15px;
            box-shadow: 0px 2px 4px #999;
            -webkit-box-shadow: 0px 2px 4px #999;
            -moz-box-shadow: 0px 2px 4px #999;

            filter: alpha(opacity = 50);
            -moz-opacity: 0.5;
            -khtml-opacity: 0.5;
            opacity: 0.5;
        }
        #login-table {
            font-size: 14px;
            color: #fff;
            font-weight: bold;
            font-family: "微软雅黑", "黑体", "宋体";

            margin-left: auto;
            margin-right: auto;
            margin-top: 20px;
        }
        #login-table input {
            width: 300px;
            height: 20px;

            font-weight: bold;
            font-family: "微软雅黑", "黑体", "宋体";
            font-size: 14px;

            border-radius: 5px 5px 5px 5px;
            -ms-border-radius: 5px 5px 5px 5px;
            -moz-border-radius: 5px 5px 5px 5px;
            -webkit-border-radius: 5px 5px 5px 5px;
            -khtml-border-radius: 5px 5px 5px 5px;

            border: none;
        }
        #btn-panel {
            font-weight: bold;
            font-family: "微软雅黑", "黑体", "宋体";
            font-size: 14px;
            color: #fff;

            width: 350px;
            margin-left: auto;
            margin-right: auto;
            margin-top: 20px;
        }
        #copyright {
            text-align: right;
            margin-top: 20px;
            font-size: 9px;
            font-weight: normal;
        }
        a { text-decoration: none; }
        a:link { color: #fff; }
        a:visited { color: #fff; }
        a:hover { color: #ddd; }

        #tip-panel {
            border: 1px solid #ff0000;
            border-radius: 5px 5px 5px 5px;
            -ms-border-radius: 5px 5px 5px 5px;
            -moz-border-radius: 5px 5px 5px 5px;
            -webkit-border-radius: 5px 5px 5px 5px;
            -khtml-border-radius: 5px 5px 5px 5px;

            width: 300px;
            background: #F7DF84;

            margin-left: auto;
            margin-right: auto;
            margin-top: 5px;
        }
        #tip-panel span {
            margin-left: 10px;
            margin-right: 10px;

            font-weight: bold;
            font-family: "微软雅黑", "黑体", "宋体";
            color: #ff0000;
            font-size: 12px;

            line-height: 30px;
        }
    </style>
</head>
<body>
<div id="login-panel">
    <table id="login-table" width="300">
        <tr>
            <td>
                Username
            </td>
        </tr>

        <tr>
            <td>
                <input type="text" name="username" />
            </td>
        </tr>

        <tr>
            <td>
                Password
            </td>
        </tr>

        <tr>
            <td>
                <input type="password" name="password" />
                {__TOKEN__}
            </td>
        </tr>
    </table>

    <div id="tip-panel" style="display: none;">
        <span><img src="__ROOT__/images/loading.gif" alt="Loading"> Waiting...</span>
    </div>

    <div id="btn-panel">
        <div class="fl"><a href="<?php echo U('Home-Index/index');?>" title="Home">Back</a></div>
        <div class="fr"><a href="#" title="Log in" id="login-btn">Log in</a></div>
        <div class="cl"></div>

        <div id="copyright">
            <?php echo ($webname); ?><br />
            Powered by <a href="mailto:admin@xcoder.in" target="_blank" title="Email me">Zhu Kaidi</a>
        </div>
    </div>
</div>
</body>
</html>