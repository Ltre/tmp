<?php /* Smarty version Smarty-3.1.13, created on 2018-03-24 15:20:03
         compiled from "/data_dev/15918716484/code/ouj_resource/svndir-biz-match_guess/frontend/protected/views/layout.html" */ ?>
<?php /*%%SmartyHeaderCode:1481575905ab5fc237ed101-57501496%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4e71414b769e1e00748ad3907fa201c53a7e630e' => 
    array (
      0 => '/data_dev/15918716484/code/ouj_resource/svndir-biz-match_guess/frontend/protected/views/layout.html',
      1 => 1511767260,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1481575905ab5fc237ed101-57501496',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '__template_file' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_5ab5fc2394d0e4_36007238',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5ab5fc2394d0e4_36007238')) {function content_5ab5fc2394d0e4_36007238($_smarty_tpl) {?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>后台</title>
</head>
<body>
<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
           
        </div>

    </div>
</div>
<div class="container">
<?php ob_start();?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['__template_file']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php $_tmp1=ob_get_clean();?><?php echo $_smarty_tpl->getSubTemplate ($_tmp1, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</div>
</body>
</html><?php }} ?>