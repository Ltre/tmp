<?php
class SmsCode {
    
    protected $uid;         //用户uid，用于监控防刷
    protected $phone;       //手机
    protected $expire;      //验证码过期时间(秒)
    protected $delay;       //发送间隔(秒)
    protected $biz;         //所属业务，用于区分短信所属业务
    protected $procedure;   //业务内某过程，用于更细致区分短信所属业务
    
    public function __construct(array $data){
        if (! isset($data['phone'])) throw new Exception('phone must be not null');
        $this->phone = $data['phone'];
        $this->expire = $data['expire'] ?: 3600;
        $this->delay = $data['delay'] ?: 60;
        $this->biz = $data['biz'] ?: '';
        $this->procedure = $data['procedure'] ?: '';
    }
    
    
    //发送验证码
    public function send(){
        $handle = $this->_getCacheHandle();
        $lastTime = obj('Cache')->getOther($handle, 'lastTime');
        if (time() - $lastTime < $this->delay) {
            return array('msg' => '请求过于频繁', 'rs' => false, 'extra' => '');
        }
        $content = substr(str_repeat('0', ($bits = 6) - 1) . rand(0, pow(10, $bits + 1) - 1), - $bits);
        $ret = obj('dwSMS')->send($this->phone, $content);

        //var_log($content, "smscode {$this->phone}， ret is {$ret}");
        
        if (1 != $ret) {
            return array('msg' => '发送失败', 'rs' => false, 'extra' => serialize($ret));
        }
        obj('Cache')->setOther($handle, 'lastTime', time());
        obj('Cache')->setOther($handle, 'lastPhone', $this->phone);
        obj('Cache')->setOther($handle, 'lastCode', $content);
        return array('msg' => '发送成功', 'rs' => true);
    }
    
    
    //核对验证码，可决定验证成功后是否销毁验证码
    public function check($code, $destory = true){
        $handle = $this->_getCacheHandle();
        $lastTime = obj('Cache')->getOther($handle, 'lastTime');
        if (time() - $lastTime > $this->expire) {
            return array('msg' => '验证码过期', 'rs' => false);
        }
        $lastPhone = obj('Cache')->getOther($handle, 'lastPhone');
        $lastCode = obj('Cache')->getOther($handle, 'lastCode');
        if ($lastCode != $code || $lastPhone != $this->phone) {
            return array('msg' => '校验出错', 'rs' => false);
        }
        if ($destory) {
            obj('Cache')->setOther($handle, 'lastTime', null);
            obj('Cache')->setOther($handle, 'lastPhone', null);
            obj('Cache')->setOther($handle, 'lastCode', null);
        }
        return array('msg' => '验证成功', 'rs' => true);
    }
    
    
    protected function _getCacheHandle(){
        if (empty($this->biz) && empty($this->procedure)) {
            $refer = $_SERVER['HTTP_REFERER'];
            $handle = 'smscode'.$refer.$this->uid;
        } else {
            $handle = 'smscode'.$this->biz.$this->procedure.$this->uid;
        }
        return $handle;
    }
    
}