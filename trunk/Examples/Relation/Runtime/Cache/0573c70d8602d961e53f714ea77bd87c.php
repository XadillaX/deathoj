<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
 <head>
  <title> ThinkPHP示例：关联操作< </title>
<link rel='stylesheet' type='text/css' href='__PUBLIC__/Css/common.css'>
<script src="__PUBLIC__/Js/Base.js"></script>
<script src="__PUBLIC__/Js/prototype.js"></script>
<script src="__PUBLIC__/Js/mootools.js"></script>
<script src="__PUBLIC__/Js/Ajax/ThinkAjax.js"></script>
<script src="__PUBLIC__/Js/Form/CheckForm.js"></script>
 </head>
 <body>
  <div style="font-size:14px;color:#000;font-family:微软雅黑,Verdana;">
  <div style="color:red;font-weight:bold;margin:12px 0px">下面演示了如何使用ThinkPHP的关联操作</div>
<?php echo ($info1); ?>  <br>
  查询用户ID为<?php echo ($id); ?>的所有关联数据：
  <?php echo (dump($user1)); ?><hr>
</div>
  查询用户ID为<?php echo ($id); ?>的用户档案关联数据：<br>
  <?php echo (dump($user2)); ?><hr>
  查询用户的数据集关联数据：
  <?php echo (dump($list)); ?>
  查询更新后的用户ID为<?php echo ($id); ?>的所有关联数据：<br>
  <?php echo (dump($user3)); ?><hr>
  <?php echo ($info2); ?>
<hr>
   示例源码<br/>控制器IndexAction类<br/><?php highlight_file(LIB_PATH.'Action/IndexAction.class.php'); ?><br/>模型类
   	<br/> <?php highlight_file(LIB_PATH.'Model/MemberModel.class.php'); ?>
  </div>
 </body>
</html>