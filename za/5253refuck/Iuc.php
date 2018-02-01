<?php
/**
 * 通用图片上传类（重构的新版，为此辣鸡项目重新定制，去除obj写法）
 * 依赖项：dwImagick, dwFile, Util, $GLOBALS['storage']
 * @author Biao
 * @since 2015-9-15
 */
class ImgUpload {
    
    protected $_ret = array(
        'fileId' => '',     //接口http://imageservice.dwstatic.com/api_storage.php生成的file_id
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
        $thumbFile = __DIR__ . 'uploadimg'.(microtime(true)*10000).'.'.$this->_ret['fileExt'];
        move_uploaded_file($file['tmp_name'], $thumbFile);
        //$im = obj('dwImagick', array($thumbFile));
        $im = new dwImagick($thumbFile);
        $im->setCutType(1);
        $im->setDstImage($thumbFile);
        $im->thumbImage($width, $height);
        return $thumbFile;
    }
    
    /**
     * 通过接口上传处理程序
     * @param array $file $_FILES中具体的某个表单域
     */
    public function up($file, $width = 0, $height = 0){
        if (! $this->_check($file)) return $this->_ret;
        $localFile = $this->_resize($file, $width, $height);
        $path = $this->_calcGroupPath();
        //$ret = obj('dwFile')->uploadFile($localFile, '', $path);
        $ret = (new dwFile)->uploadFile($localFile, '', $path);
        @unlink($localFile);//删除临时图片
        $this->_ret['url'] = $ret['file_url'] ? str_replace('./', '', $ret['file_url']) : '';
        $this->_ret['url'] = $this->tranImgSrcToW25($this->_ret['url']);
        $this->_ret['fileId'] = $ret['file_id'];
        $this->_ret['msg'] = 'upload success';
        return $this->_ret;
    }

    /**
     * 通过接口上传处理程序 - 参数为图片url的情况
     */
    public function upByImgUrl($imgUrl){
        $this->_ret['fileName'] = $imgUrl;
        $dir = __DIR__ . '/cache';
        file_exists($dir) OR mkdir($dir, 0777);
        $file = "{$dir}/".microtime(1).'.imgcache';
        $c = file_get_contents($imgUrl);
        if (empty($c)) {
            $this->_ret['msg'] = '下载图片失败';
            $this->_ret['code'] = -11;
        }
        $writeRs = file_put_contents($file, $c);
        if (false === $c) {
            $this->_ret['msg'] = '图片缓存写入失败，导致无法上传';
            $this->_ret['code'] = -12;
        }
        $this->_ret['fileSize'] = filesize($file);
        $getImaSize = getimagesize($file);
        if (false === $getImaSize) {
            $this->_ret['msg'] = '该URL，不是图片文件';
            $this->_ret['code'] = -13;
        }
        list($width, $height, $type) = $getImaSize;
        $this->_ret['width'] = $width;
        $this->_ret['height'] = $height;
        $this->_ret['mimeType'] = image_type_to_mime_type($type);
        $this->_ret['fileExt'] = ltrim(image_type_to_extension($type), '.');//fileExt必须在_calcGroupPath之前得出
        $path = $this->_calcGroupPath();
        $ret = (new dwFile)->uploadFile($file, '', $path);
        @unlink($file);
        $this->_ret['url'] = $ret['file_url'] ? str_replace('./', '', $ret['file_url']) : '';
        $this->_ret['url'] = $this->tranImgSrcToW25($this->_ret['url']);
        $this->_ret['fileId'] = $ret['file_id'];
        $this->_ret['msg'] = 'upload success';
        return $this->_ret;
    }
    
    //客户端调用接口测试用例
    static function testClient(){
        $client = new ImgUploadClient();
        $ret = $client->up($_FILES['tu'], 100, 100);
        dump($ret);
    }

    //测试upByImgUrl
    static function testUpByImgUrl(){
        $client = new ImgUploadClient();
        $ret = $client->upByImgUrl(arg('imgUrl'));
        dump($ret);
    }
    
    
    /**
     * 替换地址“s*.dwstatic.com/”为“w2|w5.dwstatic.com/s*_dwstatic/”，用于图片压缩截取
     * 参考wiki：http://dev.webdev.ouj.com/doc/ips.html
     *
     * @param string $url 原图地址
     * @param integer $w 缩放至目标宽度度(px)
     * @param integer $h 缩放至目标高度(px)
     * @return string 新地址
     */
    public function tranImgSrcToW25($url, $w = 0, $h = 0){
        $url = preg_replace('/^(https?\:\/\/)s(\d+)\.dwstatic\.com\//', '$1w2.dwstatic.com/s$2_dwstatic/', $url);
        //如果发生了地址更改，且同时指定了宽、高，则缩放
        if ($w > 0 && $h > 0 && preg_match('/^https?\:\/\/w2\.dwstatic\.com\/s\d+\_dwstatic/', $url)) {
            $url .= (preg_match('/\?/', $url) ? '&' : '?') . "imageview/0/w/{$w}/h/{$h}";
        }
        return $url;
    }
    
}

