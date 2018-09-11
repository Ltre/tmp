<?php

//结合Bootstrap 4 的分页
function smarty_function_bs4page($params) {
    if( empty($params['page']) ) return '';

    $page = $params['page'];
    $p = empty($params['_p']) ? 'p' : $params['_p'];
    $route = $params['r'];
    unset($params['page'], $params['_p'], $params['r']);

    $page_str='<ul class="pagination">';
    if($page['current_page']>1) {
        $params[$p] = $page['prev_page'];
        $page_str .= '<li class="page-item"><a class="page-link" href="' . url($route, $params) . '" rel="prev" title="上一页">上一页</a></li>';
        unset($params[$p]);
    }

    foreach($page['all_pages'] as $value) {
        if($value>1) $params[$p]=$value;
        if( $value == $page['current_page'] ) {
            $current = 'title="已经是当前页" class="page-link current" ';
            $page_str .= '<li class="page-item active"><a href="' . url($route, $params) . '" ' . $current . '>' . $value . '</a></li>';
        } else {
            $current = 'title="第'. $value .'页" class="page-link" ';
            $page_str .= '<li class="page-item"><a href="' . url($route, $params) . '" ' . $current . ' >' . $value . '</a></li>';
        }

    }
    if($page['current_page']<$page['total_page']){
        $params[$p] = $page['next_page'];
        $page_str .= '<li class="page-item"><a class="page-link" href="' . url($route, $params) . '" rel="next" title="下一页">下一页</a></li>';
    }
    $page_str .= "<li class='page-item disabled'><a class='page-link'>共{$page['total_count']}条记录  共{$page['total_page']}页</a></li>";
    return $page_str."</ul>";
}


/**
 * 结合Bootstrap 4 的分页 Version2，支持打包的array参数“__params__”
 * 例如：
 *      XxxController.php: 
 *          $this->params = ['a' => 123, 'b' => 456, 'c' => 789];
 *      xxx.html:
 *          <{bs4page page=$page r="encour/progresslist" __params__=$params}>
 */
function smarty_function_bs4pageV2($params) {
    if( empty($params['page']) ) return '';

    $page = $params['page'];
    $p = empty($params['_p']) ? 'p' : $params['_p'];
    $route = $params['r'];
    unset($params['page'], $params['_p'], $params['r']);

    //支持打包的array参数
    if (isset($params['__params__'])) {
        $params = array_merge($params, $params['__params__']);
        unset($params['__params__']);
    }

    $page_str='<ul class="pagination">';
    if($page['current_page']>1) {
        $params[$p] = $page['prev_page'];
        $page_str .= '<li class="page-item"><a class="page-link" href="' . url($route, $params) . '" rel="prev" title="上一页">上一页</a></li>';
        unset($params[$p]);
    }

    foreach($page['all_pages'] as $value) {
        if($value>1) $params[$p]=$value;
        if( $value == $page['current_page'] ) {
            $current = 'title="已经是当前页" class="page-link current" ';
            $page_str .= '<li class="page-item active"><a href="' . url($route, $params) . '" ' . $current . '>' . $value . '</a></li>';
        } else {
            $current = 'title="第'. $value .'页" class="page-link" ';
            $page_str .= '<li class="page-item"><a href="' . url($route, $params) . '" ' . $current . ' >' . $value . '</a></li>';
        }

    }
    if($page['current_page']<$page['total_page']){
        $params[$p] = $page['next_page'];
        $page_str .= '<li class="page-item"><a class="page-link" href="' . url($route, $params) . '" rel="next" title="下一页">下一页</a></li>';
    }
    $page_str .= "<li class='page-item disabled'><a class='page-link'>共{$page['total_count']}条记录  共{$page['total_page']}页</a></li>";
    return $page_str."</ul>";
}


function getIP(){
	if (!empty($_SERVER['HTTP_X_REAL_IP'])){ 
		$ip = $_SERVER['HTTP_X_REAL_IP'];
	}elseif (!empty($_SERVER['HTTP_CDN_SRC_IP'])){ //ip from cdn
		$ip = $_SERVER['HTTP_CDN_SRC_IP'];
	}elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){  //to check ip is pass from proxy
		$ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
		for ($i = (count($ips)-1); $i >= 0; $i--) {
			if (!preg_match("/^(10|172\.16|192\.168)\./", $ips[$i])){
				$ip = $ips[$i];
				 break;
			}
		}
	}
	if( !@$ip ){
		if(!empty($_SERVER['HTTP_CLIENT_IP'])){ //check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}else{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
	}
	return $ip;
}
