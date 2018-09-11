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
        if (! key_exists($username, $GLOBALS['adminList'])) {
            return false;//不在配置列表里声明的，就不是管理员
        }
        $setup = $GLOBALS['adminList'][$username];
        $ac_id = obj('Activity')->getCurrActivity();
        $inAcList = in_array($ac_id, $setup['acList']);
        $superRoutes = $GLOBALS['role_authority']['superAdmin'];
        $regularRoutes = $GLOBALS['role_authority']['regular'];
        $route = strtolower($route);
        if (in_array($route, $superRoutes)) {//验证超管操作: 仅需验证是否为超管，不需要验证活动列表
            if ($setup['superAdmin']) {
                return true;
            } else {
                return false;
            }
        } elseif (in_array($route, $regularRoutes)) {//验证普管操作：具备超管权限的，或普管有对应活动权限的
            if ($setup['superAdmin'] || $inAcList) {
                return true;
            } else {
                return false;
            }
        } else {//当前路由没在操作限制范围内，可直接通过
            return true;
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
            'create_time' => time(),
            'update_time' => time(),
        ];
        if ($find) {
            $this->update(['yyuid' => $yyuid], $data);
        } else {
            $this->insert(['yyuid' => $yyuid] + $data);
        }
        return ['yyuid' => $yyuid] + $data;
    }

}