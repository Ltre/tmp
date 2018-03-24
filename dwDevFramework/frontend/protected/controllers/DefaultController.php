<?php
class DefaultController extends BaseController {
	
	function actionIndex(){
		echo "hello world";
	}

	public function actionLogin(){
		$this->host = $_SERVER['HTTP_HOST'];
    }

	function actionLogout(){
		$domain = $GLOBALS['product_domain'];
		setcookie('yyuid','',time()-3600,'/', $domain);
		setcookie('username','',time()-3600,'/', $domain);
		setcookie('password','',time()-3600,'/', $domain);
		setcookie('osinfo','',time()-3600,'/', $domain);
		setcookie('oauthCookie','',time()-3600,'/', $domain);
		obj('dwSession')->set('userInfo', array());
        $url = arg('refer', '/');
		$url = str_replace('http://'.$_SERVER['HTTP_HOST'], '', $url);
		if( preg_match("/^(http:\/\/)/i", $url) ){
			$url = '/';
		}
		header("Location:".$url);
	}

}