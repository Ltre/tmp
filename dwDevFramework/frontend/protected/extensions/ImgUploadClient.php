<?php
/**
 * 通用图片上传类（与duowanvideo项目不同，改版本用的API不是api_storage.php,而是upload.do）
 * @author pio
 * @since 2017-01-14
 */
class ImgUpload {
    
    protected $_ret = array(
        'code' => 0,        //状态码
        'msg' => '',        //提示
        'url' => '',        //图片链接
        'width' => 0,       //宽度
        'height' => 0,      //高度
        'mimeType' => '',   //媒体类型
        'fileExt' => '',    //文件扩展名
        'fileSize' => 0,    //文件字节数
        'fileName' => '',   //客户端文件命名
    );
    
    //条件限制
    protected $_limit = array(
    	'minWidth' => 0,            //最小宽度
        'minHeight' => 0,           //最小高度
        'maxSize' => 2097152,       //最大2M
        'proportion' => array(0, 0),//宽高比
    );
    
    protected function _checkType($file){
        switch (strtolower($file['type'])) {
            case 'image/jpeg': $this->_ret['fileExt'] = 'jpg'; break;
            case 'image/gif': $this->_ret['fileExt'] = 'gif'; break;
            case 'image/png': $this->_ret['fileExt'] = 'png'; break;
            case 'image/bmp': $this->_ret['fileExt'] = 'bmp'; break;
            case 'image/tiff': $this->_ret['fileExt'] = 'tif'; break;
        }
        if (! $this->_ret['fileExt']) {
            $this->_ret['msg'] = "非法文件类型 [{$file['type']}]";
            return false;
        }
        return true;
    }
    
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
    
    //检测图片内容
    protected function _checkImgData($file){
        @$getImaSize = getimagesize($file['tmp_name']);
        if (false === $getImaSize) {
            $this->_ret['msg'] = '不是图片文件';
            return false;
        }
        
        list($width, $height, $type, $attr) = $getImaSize;
        $this->_ret['width'] = $width;
        $this->_ret['height'] = $height;
        $imgTypes = array(
            IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_SWF,
            IMAGETYPE_BMP, IMAGETYPE_TIFF_II, IMAGETYPE_TIFF_MM, IMAGETYPE_JPC,
            IMAGETYPE_JP2, IMAGETYPE_JPX, IMAGETYPE_IFF, IMAGETYPE_ICO,
        );
        if (in_array($type, $imgTypes)) {
            $this->_ret['mimeType'] = image_type_to_mime_type($type);
            if ($type == IMAGETYPE_GIF) {
                $gif = file_get_contents($file['tmp_name']);
                $rs = preg_match('/<\/?(script){1}>/i',$gif);
                if ($rs) {
                    $this->_ret['msg'] = '非法图片内容';
                    return false;
                }
            }
            return true;
        } else {
            $this->_ret['msg'] = '非法 MIMETYPE';
            return false;
        }
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
    
    //检测图片尺寸、比例
    protected function _checkDimension($file){
        $minW = $this->_limit['minWidth'];
        $minH = $this->_limit['minHeight'];
        if ($minW > 0 && $minH > 0 && ($this->_ret['width'] < $minW || $this->_ret['height'] < $minH)) {
            $this->_ret['msg'] = "图片宽高应不低于{$this->_limit['minWidth']}×{$this->_limit['minHeight']}";
            return false;
        }
        list($pW, $pH) = $this->_limit['proportion'];
        if ($pH > 0 && $pW > 0) {
            $accuracy = abs($this->_ret['width'] / $this->_ret['height'] - $pW / $pH);
            if ($accuracy > 0.2) {
                $this->_ret['msg'] = "图片宽高比要接近{$pW}:{$pH}";
                return false;
            }
        }
        return true;
    }
    
    protected function _check($file){
        if (! $this->_checkType($file)) {
            $this->_ret['code'] = -1;
            return false;
        }
        if (! $this->_checkError($file)) {
            $this->_ret['code'] = -2;
            return false;
        }
        if (! $this->_checkName($file)) {
            $this->_ret['code'] = -3;
            return false;
        }
        if (! $this->_checkTmpName($file)) {
            $this->_ret['code'] = -4;
            return false;
        }
        if (! $this->_checkImgData($file)) {
            $this->_ret['code'] = -5;
            return false;
        }
        if (! $this->_checkSize($file)) {
            $this->_ret['code'] = -6;
            return false;
        }
        if (! $this->_checkDimension($file)) {
            $this->_ret['code'] = -7;
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
 * 图片上传客户端
 */
class ImgUploadClient extends ImgUpload {
    
    //自动生成分组路径
    protected function _calcGroupPath(){
        $now = microtime(true);
        $timestamp = intval($now);
        $name1 = date('Ymd', $timestamp);
        $name2 = date('H', $timestamp);
        $name3 = date('is') . intval(($now - $timestamp) * 1000);
        $group = "{$name1}/{$name2}/{$name3}.{$this->_ret['fileExt']}";
        return $group;
    }

    
    //按需截图(注意：调用后，需unlink所生成图片)
    protected function _resize($file, $width = 0, $height = 0){
        if (! $width || ! $height) {
            return $file['tmp_name'];
        }
        if ($this->_ret['width'] < $width || $this->_ret['height'] < $height){
            return $file['tmp_name'];//图片过小
        }
        $thumbFile = BASE_DIR . 'protected/data/tmp/uploadimg'.(microtime(true)*10000).'.'.$this->_ret['fileExt'];
        move_uploaded_file($file['tmp_name'], $thumbFile);
        $im = obj('dwImagick', array($thumbFile));
        $im->setCutType(1);
        $im->setDstImage($thumbFile);
        $im->thumbImage($width, $height);
        return $thumbFile;
    }
    

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
    function up($file, $width = 0, $height = 0){
        if (! $this->_check($file)) return $this->_ret;
        
        $file['tmp_name'] = $this->_resize($file, $width, $height);

        //打日志
        obj('TmpLog')->add('WHATTHEFUCK_IMG', print_r(array('thisret' => $this->_ret, 'file' => $file), true));

        /*@$ret = $this->_postFile($GLOBALS['image_service']['url'], array(
            'filedata' => class_exists('CURLFile', false) 
                ? new CURLFile(
                    realpath($file['tmp_name']), 
                    $this->_ret['mimeType'], 
                    intval(microtime(1)*1000).'.'.$this->_ret['fileExt']
                ) 
                : ('@'.$file['tmp_name'].';type='.$this->_ret['mimeType']),
        ));*/
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

    
    //客户端调用接口测试用例
    static function testClient(){
        $client = new ImgUploadClient();
        $ret = $client->up($_FILES['filedata'], 100, 100);
        dump($ret);
    }
}

