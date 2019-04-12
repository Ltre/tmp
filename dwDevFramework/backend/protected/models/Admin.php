<?php

class Admin extends Model {

    protected $table_name = 'admin';

    public function isLogin(){
        if (!empty($_COOKIE['username']) || !empty($_COOKIE['lg_token'])) {
            @$user = obj('dwSession')->get('userInfo');
			//已经登录 (优先判断新版登录的cookie.lg_uid，否则判断旧版登录的cookie.username)
			if( !empty($user) && (
                @isset($_COOKIE['lg_uid']) && @$user['yyuid'] == @$_COOKIE['lg_uid'] 
                || 
                @isset($_COOKIE['username']) && @$user['udb_name']==@$_COOKIE['username']
            )){
				return $user;
			}

            //session没有数据，或者切换用户，重新验证cookie
            $udb = obj('dwUDB')->isLogin();
            if ($udb) {
                $user = $this->getRawUser($udb['yyuid']);
                if (empty($user)) {
                    @$info = obj('Util')->getInfoByUDBProxy();
                    $user = $this->setNewUser($udb, $info);
                }
				@obj('dwSession')->set('userInfo', $user);
				return $user;
            }
        }
        
        return [];
    }


    //检测权限
    public function checkAuthority($username, $route){
        $route = strtolower($route);
        $superRoutes = $GLOBALS['role_authority']['superAdmin'];
        $regularRoutes = $GLOBALS['role_authority']['regular'];
        if (! key_exists($username, $GLOBALS['adminList'])) {//遇到查询员
            if (in_array($route, array_merge($superRoutes, $regularRoutes))) {
                return [false, '权限不足，必须是管理员'];//拒绝[查询员]访问[超管]和[普管]的权限范围
            } else {
                return [true, '校验成功，当前为查询员'];//允许查询员访问其他不重要的范围
            }
        }

        $setup = $GLOBALS['adminList'][$username];

        //验证特殊权限
        $currSp = 'something2';//@todo 获取当前会使用到的特殊权限（具体逻辑待定，执行代码可以独立到一个新的文件里）
        $checkSp = null !== $currSp && in_array($currSp, $setup['spList']);

        //分别验证超管和普管
        if (in_array($route, $superRoutes)) {//验证超管操作

            if ($setup['superAdmin']) {
                return [true, 'ok'];
            } else {
                return [true, '权限不足，不是超管'];
            }

        } else {//验证普管操作：超管可以访问普管的权限，且不受特殊权限校验的影响；普管如踩中特殊权限，则需要验证
            
            if (! $setup['superAdmin'] && ! $checkSp) {
                return [false, '未配置此特殊权限'];
            } else {
                return [true, 'ok'];
            }

        }
    }


	/**
	 * 获取用户库的源数据
	 */
	public function getRawUser($uid){
        $user = $this->find(['yyuid' => $uid]) ?: [];
        return $user;
	}

    public function setNewUser($udbdata, $info){
        $nickname = $info['nickname'] ?: $info['username'];
        $avatar = $info['avatar'];
        $udb = $udbdata['username'];
        $yyuid = $udbdata['yyuid'];
        $find = $this->find(['yyuid' => $yyuid]);
        $data = [
            'udb' => $udb,
            'nickname' => $nickname,
            'avatar' => $avatar,
            'created' => time(),
            'updated' => time(),
        ];
        if ($find) {
            $this->update(['yyuid' => $yyuid], $data);
        } else {
            $this->insert(['yyuid' => $yyuid] + $data);
        }
        return ['yyuid' => $yyuid] + $data;
    }

}