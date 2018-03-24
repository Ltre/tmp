<?php /* Smarty version Smarty-3.1.13, created on 2018-03-24 15:27:19
         compiled from "/data_dev/15918716484/code/ouj_resource/svndir-biz-match_guess/frontend/protected/views/default_login.html" */ ?>
<?php /*%%SmartyHeaderCode:3310790525ab5fc23996860-72288598%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8b8d6b2c49582f86ac37898417b043a81471860e' => 
    array (
      0 => '/data_dev/15918716484/code/ouj_resource/svndir-biz-match_guess/frontend/protected/views/default_login.html',
      1 => 1521876438,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3310790525ab5fc23996860-72288598',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_5ab5fc2399fba0_14704688',
  'variables' => 
  array (
    'host' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5ab5fc2399fba0_14704688')) {function content_5ab5fc2399fba0_14704688($_smarty_tpl) {?><html>
    <head>
        <title></title>
    </head>
    <body>
        <button id="in">login</button>
        <button id="out">logout</button>
        <script src="http://pub.dwstatic.com/common/js/jquery.js"></script>
        <script src="http://pub.dwstatic.com/common/js/dwudbproxy.js"></script>
        <script>
            $('#in').click(function(){
                dwUDBProxy.login("/");
            });
            $('#out').click(function(){
                dwUDBProxy.logout("//<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['host']->value, ENT_QUOTES, 'UTF-8', true);?>
/logout")
            });
        </script>
    </body>
</html><?php }} ?>