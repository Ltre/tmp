<?php

class User extends Model {

    protected $table_name = 'user';

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