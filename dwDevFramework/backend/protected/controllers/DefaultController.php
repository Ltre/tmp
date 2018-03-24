<?php
class DefaultController extends BaseController {
	
	function actionIndex(){
		// echo "hello admin";
	}

	public function actionLogin(){
		$this->host = $_SERVER['HTTP_HOST'];
    }

	function actionLogout(){
		$host = $_SERVER['HTTP_HOST'];
		setcookie('yyuid','',time()-3600,'/', $host);
		setcookie('username','',time()-3600,'/', $host);
		setcookie('password','',time()-3600,'/', $host);
		setcookie('osinfo','',time()-3600,'/', $host);
		setcookie('oauthCookie','',time()-3600,'/', $host);
		obj('dwSession')->set('userInfo', array());
        $url = arg('refer', '/');
		$url = str_replace('http://'.$host, '', $url);
		if( preg_match("/^(http:\/\/)/i", $url) ){
			$url = '/';
		}
		header("Location:".$url);
	}

}