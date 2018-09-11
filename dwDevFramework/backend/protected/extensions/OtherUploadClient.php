<?php
/**
 * 通用文件上传类（用的API不是api_storage.php,而是upload.do）
 * @author pio
 * @since 2017-08-11
 */
class OtherUpload {
    
    protected $_ret = array(
        'code' => 0,        //状态码
        'msg' => '',        //提示
        'url' => '',        //链接
        'mimeType' => '',   //媒体类型
        'fileExt' => '',    //文件扩展名
        'fileSize' => 0,    //文件字节数
        'fileName' => '',   //客户端文件命名
    );
    
    /**
     * 条件限制
     *      maxSize -> 文件字节数限制
     *      fileExt -> 用户定义的可匹配扩展名正则，及每个正则对应的异常处理回调方法。
     *                 注意：系统将优先排除预定义的非法扩展名，再应用此处的规则。
     *                 配置格式如下：
     *                      '正则表达式' => function($regExp, $ext){ 异常处理过程；return '提示信息'; }
     *                 回调方法将被传入：当前拦截的正则[$regExp]；此文件扩展名[$ext]。
     *                 例如：
     *                      '/^\w+$/' => function($regExp, $ext){ return '不能上传没扩展名的文件!'; }
     *                      '/^mp4$/' => function($regExp, $ext){ return '仅限上传MP4文件!'; }
     */
    protected $_limit = array(
    	'minWidth' => 0,            //最小宽度
        'minHeight' => 0,           //最小高度
        'proportion' => array(0, 0),//宽高比
        'maxSize' => 2097152,       //文件大小
        'fileExt' => array(),       //扩展名限定
    );

    //检查错误码，待完善
    protected function _checkError($file){
        if (UPLOAD_ERR_OK == $file['error']) return true;
        
        $this->_ret['msg'] = "上传出错，错误码{$file['error']}"; // ...各种不成功的情况
        
        return false;
    }
    
    protected function _checkName($file){
        if (preg_match('/\\0|\:|\/|\\|\?|\^|\*|\<|\>|\$/', $file['name'])) {
            $this->_ret['msg'] = '非法文件名';
            return false;
        }
        $this->_ret['fileName'] = $file['name'];
        return true;
    }
    
    protected function _checkTmpName($file){
        if (is_uploaded_file($file['tmp_name'])) return true;
        $this->_ret['msg'] = '非正常途径上传';
        return false;
    }
    
    //检测文件大小
    protected function _checkSize($file){
        $this->_ret['fileSize'] = $file['size'];
        if ($file['size'] > $this->_limit['maxSize']) {
            $this->_ret['msg'] = "文件不能超过 {$this->_limit['maxSize']} 字节";
            return false;
        }
        return true;
    }

    //检测扩展名
    protected function _checkExt($file){
        preg_match('/\.(\w+)$/', $file['name'], $matches);
        $ext = $matches[1];
        $this->_ret['fileExt'] = $ext;
        if (empty($ext)) {
            $this->_ret['msg'] = "不能上传无扩展名的文件";
            return false;
        }
        if (preg_match('/^(php|html|phtml|php3|jsp|asp|htm|js|java|sh|bat)$/i', $ext)) {
            $this->_ret['msg'] = "非法扩展名[{$ext}]";
            return false;
        }
        foreach ($this->_limit['fileExt'] as $regExp => $callback) {
            if (! preg_match($regExp, $ext)) {
                $this->_ret['msg'] = call_user_func($callback, $regExp, $ext);
                return false;
            }
        }
        return true;
    }

    //检测图片尺寸、比例
    protected function _checkDimension($file){
        @$getImaSize = getimagesize($file['tmp_name']);
        if (false === $getImaSize) {
            return true;//忽略非图片文件
        }
        list($width, $height, $type, $attr) = $getImaSize;

        $minW = $this->_limit['minWidth'];
        $minH = $this->_limit['minHeight'];
        if ($minW > 0 && $minH > 0 && ($width < $minW || $height < $minH)) {
            $this->_ret['msg'] = "图片宽高应不低于{$this->_limit['minWidth']}×{$this->_limit['minHeight']}";
            return false;
        }
        list($pW, $pH) = $this->_limit['proportion'];
        if ($pH > 0 && $pW > 0) {
            $accuracy = abs($width / $height - $pW / $pH);
            if ($accuracy > 0.2) {
                $this->_ret['msg'] = "图片宽高比要接近{$pW}:{$pH}";
                return false;
            }
        }
        return true;
    }
    
    protected function _check($file){
        if (! $this->_checkError($file)) {
            $this->_ret['code'] = -1;
            return false;
        }
        if (! $this->_checkName($file)) {
            $this->_ret['code'] = -2;
            return false;
        }
        if (! $this->_checkTmpName($file)) {
            $this->_ret['code'] = -3;
            return false;
        }
        if (! $this->_checkSize($file)) {
            $this->_ret['code'] = -4;
            return false;
        }
        if (! $this->_checkExt($file)) {
            $this->_ret['code'] = -5;
            return false;
        }
        if (! $this->_checkDimension($file)) {
            $this->_ret['code'] = -6;
            return false;
        }
        return true;
    }
    
    //可在处理图片前设定限制
    public function limit($limit = array()){
        foreach ($limit as $k => $v) {
            if (isset($limit[$k]) && isset($this->_limit[$k])) {
                $this->_limit[$k] = $v;
            }
        }
    }
    
}

/**
 * 上传客户端
 */
class OtherUploadClient extends OtherUpload {
    

    protected function _postFile($url, $post = array(), $timeout = 59){
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($c, CURLOPT_POSTFIELDS, $post);
        $data = json_decode(curl_exec($c), 1);
        curl_close($c);
        return $data;
    }


    /*
     * 通过接口上传处理程序
     * @param array $_FILES中具体的某个表单域
     */
    function up($file){
        if (! $this->_check($file)) return $this->_ret;

        //打日志
        obj('TmpLog')->add('WHATTHEFUCK_FILE', print_r(array('thisret' => $this->_ret, 'file' => $file), true));

        //上传服务器不根据mimetype返回对应的扩展名，而是根据传过去的文件名。上面注释掉的代码不要删除，以备他用
        move_uploaded_file($file['tmp_name'], $file['tmp_name']=$file['tmp_name'].'.'.$this->_ret['fileExt']);
        @$ret = $this->_postFile($GLOBALS['image_service']['url'], array(
            'filedata' => class_exists('CURLFile', false) 
                ? new CURLFile(realpath($file['tmp_name']))
                : ('@'.$file['tmp_name'].';type='.$this->_ret['mimeType']),
        ));
        @unlink($file['tmp_name']);
        if ($ret['code'] != 1) {
            $this->_ret['code'] = -7;
            $this->_ret['msg'] = 'upload failed';
        } else {
            $this->_ret['msg'] = 'upload success';
            $this->_ret['url'] = $ret['url'];
        }
        
        return $this->_ret;
    }


    function upByFilePath($filepath, $mimeType){
        //上传服务器不根据mimetype返回对应的扩展名，而是根据传过去的文件名。
        @$ret = $this->_postFile($GLOBALS['image_service']['url'], array(
            'filedata' => class_exists('CURLFile', false) 
                ? new CURLFile(realpath($filepath))
                : ('@'.$filepath.';type='.$mimeType),
        ));
        @unlink($filepath);
        if ($ret['code'] != 1) {
            $this->_ret['code'] = -7;
            $this->_ret['msg'] = 'upload failed';
        } else {
            $this->_ret['msg'] = 'upload success';
            $this->_ret['url'] = $ret['url'];
        }
        
        return $this->_ret;
    }

    
    //客户端调用接口测试用例
    static function testClient(){
        $client = new OtherUploadClient();
        $ret = $client->up($_FILES['filedata']);
        dump($ret);
    }
}

