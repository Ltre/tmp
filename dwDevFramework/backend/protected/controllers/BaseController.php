<?php
class BaseController extends Controller{

	var $layout = "layout.html";

    var $yyuid;

	//需要登录的列表
	private $_mustLogin = array(
        'default/*',
        'activity/*',
        'guess/*',
        'matches/*',
        'score/*',
        'team/*',
        'tool/*',
        'user/*',
        'encour/*',
        'test/login',
    );


	//在需要登录的列表中排除不需要登录的页面				
	private $_mustLoginExclude = array(
        'default/login',
        'default/logout',
    );

	function init(){
        //按需跳转HTTPS
        $this->_gotoHttps();
        //开启通用缓存支持
        $this->setCacheAble();
        //将控制器注入全局
        $GLOBALS['controller'] = $this;
        //注入登录态
        $lgInfo = obj('Admin')->isLogin();
        $this->loginInfo = $lgInfo;
        @$this->yyuid = $lgInfo['yyuid'];
        obj('RuntimeLifeCycle')->setData('loginInfo', $lgInfo);
        @obj('RuntimeLifeCycle')->setData('yyuid', $lgInfo['yyuid']);
        //$GLOBALS全局变量从此处往后，禁止再被赋值
        $this->globals = $GLOBALS;
        //路由控制
		$route1 = CONTROLLER_NAME .'/*';
		$route2 = CONTROLLER_NAME .'/'. ACTION_NAME;
		$this->route = $route2;//共享当前路由值
        //登录拦截
		if( in_array($route1, $this->_mustLogin) || in_array($route2, $this->_mustLogin) ){
			if( in_array($route2, $this->_mustLoginExclude) ){
				return ;
			}
            if (empty($lgInfo)){
                $this->refuce('未登录', true);
            }
		} else {
            //允许$_mustLogin声明之外的不登录，且不用检测后续操作权限。
            //此处避免在访问一些不需登录的地方时，由于未登录，但还要被执行后续的多余操作：obj('Admin')->checkAuthority所带来的麻烦
            return;
        }
        //检测操作权限
        if (! obj('Admin')->checkAuthority(@$lgInfo['udb'], $this->route)) {
            $this->refuce('权限不足', false);
        }
	}


    private function refuce($msg, $relogin=true){
        if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"])=="xmlhttprequest") {
            $this->jsonOutput(array('rs' => false, 'msg' => $msg));
        } else {
            if ($relogin) {
                $this->alert($msg, "//{$_SERVER['HTTP_HOST']}/login");
            } else {
                $this->alert($msg);
            }
        }
        exit;
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
        @header('Access-Control-Allow-Origin: '.($_SERVER['HTTP_ORIGIN']?:'*'));
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


    protected function confirm($msg = null, $url = null){
        header("Content-type: text/html; charset=utf-8");
        if ($msg && $url) {
            $code = "if(confirm('{$msg}')) location.href='{$url}';";
        } elseif ($msg && null === $url) {
            $code = "alert('{$msg}'); history.go(-1);";
        } elseif (null === $msg && $url) {
            $code = "if(confirm('确定前往{$url}吗?')) location.href='{$url}'";
        } else {
            $code = '';
        }
        echo "<script>{$code}</script>";
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


    /**
     * 批量获取参数
     * @param array $fields 参数名数组
     */
    protected function getParams(array $fields){
        $data = [];
        foreach ($fields as $f) {
            if (isset($_REQUEST[$f])) {
                $data[$f] = $this->arg($f);
            }
        }
        return $data;
    }

} 