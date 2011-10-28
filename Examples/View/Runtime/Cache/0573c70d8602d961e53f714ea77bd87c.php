<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
 <head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>ThinkPHP示例：视图查询</title>
<link rel="stylesheet" type="text/css" href="__PUBLIC__/Css/common.css" />
 </head>
 <body>
 <div class="main">
 <h3>ThinkPHP示例之视图查询：视图查询</h3>
 <table cellpadding=2 cellspacing=2>
 <td></td>
	<td><hr></td>
 </tr>
  <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><tr>
  <td></td>
	<td style="border-bottom:1px dotted silver"><?php echo ($vo["title"]); ?> <span style="color:gray">[<?php echo ($vo["username"]); ?> <?php echo (date('Y-m-d H:i:s',$vo["create_time"])); ?>]</span></td>
  </tr>
  <tr >
  <td></td>
	<td><div class="content"><?php echo (nl2br($vo["content"])); ?></div></td>
  </tr><?php endforeach; endif; else: echo "" ;endif; ?>
 <tr>
 <td></td>
	<td><hr> 示例源码<br/>控制器IndexAction类<br/><?php highlight_file(LIB_PATH.'Action/IndexAction.class.php'); ?><br/>模型FormViewModel类<br/><?php highlight_file(LIB_PATH.'Model/FormViewModel.class.php'); ?></td>
 </tr>
 </table>
</div>
 </body>
</html>