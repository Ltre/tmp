<?php
class BaseController extends Controller{

	var $layout = "layout.html";
    
    var $yyuid;

	//需要登录的列表
	private $_mustLogin = array(
        'default/*',
    );


	//在需要登录的列表中排除不需要登录的页面				
	private $_mustLoginExclude = array(
        'default/login',
        'default/logout',
    );

	function init(){
        //开启通用缓存支持
        $this->setCacheAble();
        //将控制器注入全局
        $GLOBALS['controller'] = $this;
        //注入登录态
        $lgInfo = obj('User')->isLogin();
        $this->loginInfo = $lgInfo;
        @$this->uid = $lgInfo['yyuid'];
        //$GLOBALS全局变量从此处往后，禁止再被赋值
        $this->globals = $GLOBALS;
        //路由控制
		$route1 = CONTROLLER_NAME .'/*';
		$route2 = CONTROLLER_NAME .'/'. ACTION_NAME;
		$this->route = $route2;//共享当前路由值
        //客户端标识
        $this->cli = $this->getCli();
        //按需跳转HTTPS
        $this->_gotoHttps();
        //登录拦截
		if( in_array($route1, $this->_mustLogin) || in_array($route2, $this->_mustLogin) ){
			if( in_array($route2, $this->_mustLoginExclude) ){
				return ;
			}
            if (empty($lgInfo)){
                if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"])=="xmlhttprequest") {
                    $this->jsonOutput(array('rs' => false, 'msg' => '未登录'));
                } else {
                    header("location: //{$_SERVER['HTTP_HOST']}/login");
                }
                exit;
            }	
		}
	}


    public function arg($name = null, $default = null, $callback_funcname = null){
        $ret = parent::arg($name, $default, $callback_funcname);
        if( is_array($ret) ){
            array_walk($ret, function(&$v, $k){$v = trim(htmlspecialchars($v, ENT_QUOTES, 'UTF-8'));} );
        }else{
            $ret = trim(htmlspecialchars($ret, ENT_QUOTES, 'UTF-8'));
        }
        return $ret;
    }

    
	public function jsonOutput($data, $callback='callback'){
	    header("Content-Type: application/x-javascript; charset=UTF-8");
        header('Access-Control-Allow-Origin: '.($_SERVER['HTTP_ORIGIN']?:'*'));
        header('Access-Control-Allow-Credentials: true');
		$fun = $this->arg($callback);
		$json = json_encode($data);
		echo empty($fun)? $json : "{$fun}({$json})";
		exit;
	}


    protected function isPost(){
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    
    protected function redirect( $url ) {
        header('location:' . $url);
        exit;
    }


    protected function alert($msg = null, $url = null){
        header("Content-type: text/html; charset=utf-8");
        $alert_msg = null === $msg ? '' : "alert('$msg');";
        if( empty($url) ) {
            $gourl = 'history.go(-1);';
        }else{
            $gourl = "window.location.href = '{$url}'";
        }
        echo "<script>$alert_msg $gourl</script>";
        exit;
    }


	public function jump($url, $delay = 0){
        echo "<html><head><meta http-equiv='refresh' content='{$delay};url={$url}'></head><body></body></html>";
        exit;
    }


	public function goback(){
		echo '<script language="javascript">window.history.back();</script>';
		exit;
	}


    //处理OPTIONS请求，前提：r参数必须必须必须写到URL里！！！
    public function dealOptionsMethod(){
        if (strtolower($_SERVER['REQUEST_METHOD']) === 'options') {
            header('Access-Control-Allow-Origin: '.($_SERVER['HTTP_ORIGIN']?:'*'));
            header('Access-Control-Allow-Credentials: true');
            header(0, 0, 204);
            exit;
        }
    }


    //对于match-guess.duowan.com域名，强制跳转到https
    private function _gotoHttps(){
        if (! $this->isHttps() && $_SERVER['HTTP_HOST'] == $GLOBALS['product_domain']) {
            header("Location: https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");
            exit;//若出现循环跳转，则屏蔽该分支
        }
    }


    public function isHttps(){
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 1) {//apache
            return true;
        }
        if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') {//iis
            return true;
        }
        if (isset($_SERVER['HTTP_X_HTTPS_PROTOCOL']) && strtolower($_SERVER['HTTP_X_HTTPS_PROTOCOL']) == 'https') {
            return true;
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https') {
            return true;
        }
        if ($_SERVER['SERVER_PORT'] == 443) {
            return true;
        }
        return false;
    }


    //设置客户端标识
    private function getCli(){
        $ckName = $GLOBALS['web_client']['cliCookieName'];
        if (! isset($_COOKIE[$ckName])) {
            // getIP();  @todo 限制同个IP不能生成太多客户端标识，具体待定
            $cli = sha1(microtime(1).mt_rand(0, 9999));
            setcookie($ckName, $cli, time()+86400*30, '/');
            return $cli;
        } else {
            return $_COOKIE[$ckName];
        }
    }


	//全局控制缓存
	protected function setCacheAble(){
		$flag = arg('cache');
		if ('no' == $flag) {
            $GLOBALS['cache_control']['CACHE_GET_ABLE'] = false;
            $GLOBALS['cache_control']['CACHE_SET_ABLE'] = false;
		} elseif ( 'update'==$flag ) {
            $GLOBALS['cache_control']['CACHE_GET_ABLE'] = false;
            $GLOBALS['cache_control']['CACHE_SET_ABLE'] = true;
		} else {
            $GLOBALS['cache_control']['CACHE_GET_ABLE'] = true;
            $GLOBALS['cache_control']['CACHE_SET_ABLE'] = true;
		}
	}

} 