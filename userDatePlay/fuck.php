<?php
while (1)
    print_r((new dwHttp)->post('http://61.147.186.105/cron/syncUserDatePlay', ['taskId' => 'shabi', 'start' => 20160930], 55, "Host: huya.cms.duowan.com"));




class dwHttp {
	protected $way='';

	public function __construct($way=''){	
		if(in_array($way,array('curl','socket','file_get_contents'))){ //���ָ�����ʷ�ʽ����ָ���ķ�ʽȥ����
			$this->way=$way;	
		}elseif(function_exists('curl_init')){ //curl��ʽ
			$this->way='curl';
		}else if(function_exists('fsockopen')){ //socket
			$this->way='socket';
		}else if(function_exists('file_get_contents')){ //phpϵͳ����file_get_contents
			$this->way='file_get_contents';
		}else{
			$this->way='';
		}	
	}
	
	//ͨ��get��ʽ��ȡ����
	public function get($url, $timeout=5, $header="") {	
		if(empty($url)||empty($timeout)) return false;
		if(!preg_match('/^(http|https)/is',$url)) $url="http://".$url;
			
		switch($this->way){
			case 'curl':return $this->curlGet($url, $timeout, $header);break;
			case 'socket':return $this->socketGet($url, $timeout, $header);break;
			case 'file_get_contents':return $this->phpGet($url, $timeout, $header);break;
			default:return false;	
		}
	}
	
	//ͨ��POST��ʽ��������
	public function post($url, $post_data=array(), $timeout=5, $header="") {
		if(empty($url)||empty($timeout)) return false;
		if(!preg_match('/^(http|https)/is',$url)) $url="http://".$url;
		
		switch($this->way){
			case 'curl':return $this->curlPost($url, $post_data, $timeout, $header);break;
			case 'socket':return $this->socketPost($url, $post_data,$timeout, $header);break;
			case 'file_get_contents':return $this->phpPost($url, $post_data, $timeout, $header);break;
			default:return false;	
		}
	}	
	
	//�����ļ�
	public function postFile($url, $post_data=array(), $timeout=30, $cookie=''){
		$c = curl_init(); 
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($c, CURLOPT_URL, $url); 
		curl_setopt($c, CURLOPT_POST, true); 
		curl_setopt($c, CURLOPT_TIMEOUT, $timeout); 
		curl_setopt($c, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		empty($cookie) or curl_setopt($c, CURLOPT_COOKIEFILE, $cookie); 
		empty($cookie) or curl_setopt($c, CURLOPT_COOKIEJAR, $cookie); 
		curl_setopt($c, CURLOPT_POSTFIELDS, $post_data); 
		$data = curl_exec($c); 
		curl_close($c); 
		return $data;	
	}
	
	//ͨ��curl get����
	protected function curlGet($url, $timeout=5, $header="") {
		$header = empty($header) ? $this->defaultHeader() : $header;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
		curl_setopt($ch, CURLOPT_HTTPHEADER, explode("\r\n", $header));//ģ���headerͷ
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	
	//ͨ��curl post����
	protected function curlPost($url, $post_data='', $timeout=5, $header="") {
		$header = empty($header) ? $this->defaultHeader() : $header;
		$post_string = is_array($post_data) ? http_build_query($post_data) : $post_data;  
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);        
		curl_setopt($ch, CURLOPT_HTTPHEADER, explode("\r\n", $header));//ģ���headerͷ
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	
	//ͨ��socket get����
	protected function socketGet($url,$timeout=5,$header="") {
		$header = empty($header) ? $this->defaultHeader() : $header;
		$url2 = parse_url($url);
		$url2["path"] = isset($url2["path"])? $url2["path"]: "/" ;
		$url2["port"] = isset($url2["port"])? $url2["port"] : 80;
		$url2["query"] = isset($url2["query"])? "?".$url2["query"] : "";
		$host_ip = @gethostbyname($url2["host"]);

		if(($fsock = fsockopen($host_ip, $url2['port'], $errno, $errstr, $timeout)) < 0){
			return false;
		}
		
		$request =  $url2["path"] .$url2["query"];
		$in  = "GET " . $request . " HTTP/1.0\r\n";
		if(false===strpos($header, "Host:")){	
			 $in .= "Host: " . $url2["host"] . "\r\n";
		}
		
		$in .= $header;
		$in .= "Connection: Close\r\n\r\n";
		
		if(!@fwrite($fsock, $in, strlen($in))){
			@fclose($fsock);
			return false;
		}
		return $this->GetHttpContent($fsock);
	}
	
	//ͨ��socket post����
	protected function socketPost($url, $post_data='', $timeout=5, $header="") {
		$header = empty($header) ? $this->defaultHeader() : $header;
		$post_string = is_array($post_data) ? http_build_query($post_data) : $post_data;  
	
		$url2 = parse_url($url);
		$url2["path"] = ($url2["path"] == "" ? "/" : $url2["path"]);
		$url2["port"] = ($url2["port"] == "" ? 80 : $url2["port"]);
		$host_ip = @gethostbyname($url2["host"]);
		$fsock_timeout = $timeout; //��ʱʱ��
		if(($fsock = fsockopen($host_ip, $url2['port'], $errno, $errstr, $fsock_timeout)) < 0){
			return false;
		}
		$request =  $url2["path"].($url2["query"] ? "?" . $url2["query"] : "");
		$in  = "POST " . $request . " HTTP/1.0\r\n";
		$in .= "Host: " . $url2["host"] . "\r\n";
		$in .= $header;
		$in .= "Content-type: application/x-www-form-urlencoded\r\n";
		$in .= "Content-Length: " . strlen($post_string) . "\r\n";
		$in .= "Connection: Close\r\n\r\n";
		$in .= $post_string . "\r\n\r\n";
		unset($post_string);
		if(!@fwrite($fsock, $in, strlen($in))){
			@fclose($fsock);
			return false;
		}
		return $this->GetHttpContent($fsock);
	}

	//ͨ��file_get_contents����get����
	protected function phpGet($url,$timeout=5, $header="") {
		$header = empty($header) ? $this->defaultHeader() : $header;
		$opts = array( 
				'http'=>array(
							'protocol_version'=>'1.0', //httpЭ��汾(����ָ��php5.2ϵĬ��Ϊhttp1.0)
							'method'=>"GET",//��ȡ��ʽ
							'timeout' => $timeout ,//��ʱʱ��
							'header'=> $header)
				  ); 
		$context = stream_context_create($opts);    
		return  @file_get_contents($url,false,$context);
	}
	
	//ͨ��file_get_contents ����post����
	protected function phpPost($url, $post_data=array(), $timeout=5, $header="") {
		$header = empty($header) ? $this->defaultHeader() : $header;
		$post_string = is_array($post_data) ? http_build_query($post_data) : $post_data;   
		$header.="Content-length: ".strlen($post_string);
		$opts = array( 
				'http'=>array(
							'protocol_version'=>'1.0',//httpЭ��汾(����ָ��php5.2ϵĬ��Ϊhttp1.0)
							'method'=>"POST",//��ȡ��ʽ
							'timeout' => $timeout ,//��ʱʱ�� 
							'header'=> $header,  
							'content'=> $post_string)
				  ); 
		$context = stream_context_create($opts);    
		return  @file_get_contents($url,false,$context);
	}
	
	//Ĭ��ģ���headerͷ
	protected function defaultHeader(){
		$header="User-Agent:Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12\r\n";
		$header.="Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n";
		$header.="Accept-language: zh-cn,zh;q=0.5\r\n";
		$header.="Accept-Charset: GB2312,utf-8;q=0.7,*;q=0.7\r\n";
		return $header;
	}
	
	//��ȡͨ��socket��ʽget��postҳ��ķ�������
	protected function GetHttpContent($fsock=null){
		$out = null;
		while($buff = @fgets($fsock, 2048)){
			 $out .= $buff;
		}
		fclose($fsock);
		$pos = strpos($out, "\r\n\r\n");
		$head = substr($out, 0, $pos);    //http head
		$status = substr($head, 0, strpos($head, "\r\n"));    //http status line
		$body = substr($out, $pos + 4, strlen($out) - ($pos + 4));//page body
		if(preg_match("/^HTTP\/\d\.\d\s([\d]+)\s.*$/", $status, $matches)){
			if(intval($matches[1]) / 100 == 2){
				return $body;  
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}