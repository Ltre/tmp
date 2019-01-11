#!/usr/local/php/bin/php
<?php
class dwCache
{
	public $linked_object = null;
	public $cached_time = 3600;
	private $mmc = null;
	private $domain = null;
	private $version;

	function __construct($domain){
		if( !class_exists('Memcached') ){
		    $this->mmc = new Memcache();
		}else{
		    $this->mmc = new Memcached();
			$this->mmc->setOptions(array(
							Memcached::OPT_CONNECT_TIMEOUT=>2000,
							Memcached::OPT_RETRY_TIMEOUT=>2000,
							Memcached::OPT_POLL_TIMEOUT=>2000,
					));
		}
		if(defined('DWAE_MMC_HOST_1'))$this->mmc->addServer(DWAE_MMC_HOST_1, DWAE_MMC_PORT_1);
		if(defined('DWAE_MMC_HOST_2'))$this->mmc->addServer(DWAE_MMC_HOST_2, DWAE_MMC_PORT_2);
		$this->domain = $domain;
		if(!$this->version = $this->mmc->get('version_'.$domain)){
			$this->mmc->set('version_'.$domain, 1);
			$this->version = 1;
		}
	}

	function __call($name, $args){
		$cache_id = get_class($this->linked_object) . '@' . $name. '#' . print_r($args, 1);
		$result = $this->get($cache_id);
		if(DEBUG || !$result){
			$result = call_user_func_array(array($this->linked_object, $name), $args);
			$this->set($cache_id, $result, $this->cached_time);
		}
		return $result;
	}

	function set($key, $var, $expire=3600){
		if(!$this->mmc)return;
		if( !class_exists('Memcached') ){
		    return $this->mmc->set($this->domain.'_'.$this->version.'_'.$key, $var, 0, $expire);
		}else{
		    return $this->mmc->set($this->domain.'_'.$this->version.'_'.$key, $var, $expire);
		}
	}

	function get($key){
		if(!$this->mmc)return;
		return $this->mmc->get($this->domain.'_'.$this->version.'_'.$key);
	}
	
	function add($key, $var, $expire=3600){		
		if(!$this->mmc)return;		
		if( !class_exists('Memcached') ){
			return $this->mmc->add($this->domain.'_'.$this->version.'_'.$key, $var, false, $expire);		    
		}else{			
		    return $this->mmc->add($this->domain.'_'.$this->version.'_'.$key, $var, $expire);
		}
	}
	
	function incr($key, $value=1){
		if(!$this->mmc)return;
		return $this->mmc->increment($this->domain.'_'.$this->version.'_'.$key, $value);
	}

	function decr($key, $value=1){
		if(!$this->mmc)return;
		return $this->mmc->decrement($this->domain.'_'.$this->version.'_'.$key, $value);
	}

	function delete($key){
		if(!$this->mmc)return;
		return $this->mmc->delete($this->domain.'_'.$this->version.'_'.$key);
	}

	function flush(){
		if(!$this->mmc)return;
		++$this->version;
		$this->mmc->set('version_'.$this->domain, $this->version);
	}
	
	function getVersion(){
	    return $this->version;
	}
	
	function getDomain(){
	    return $this->domain ;
	}
}


if(!defined('DWAE_MMC_HOST_1'))define('DWAE_MMC_HOST_1', '10.21.46.47');
if(!defined('DWAE_MMC_PORT_1'))define('DWAE_MMC_PORT_1', 11218);
if(!defined('DWAE_MMC_HOST_2'))define('DWAE_MMC_HOST_2', '10.21.46.48');
if(!defined('DWAE_MMC_PORT_2'))define('DWAE_MMC_PORT_2', 11218);

$mmc = new dwCache('dwcn');

for ($i=0; $i < 20; $i++){
    //www.duowan.cn首页
    $articleHtml = $mmc->get('article_duowan_home');
    $html = file_get_contents('http://www.duowan.com/1704/m_355943548854.html');
    $html = preg_replace('/\<\/head\>/i', '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script><script>(adsbygoogle = window.adsbygoogle || []).push({google_ad_client: "ca-pub-7403881508152497", enable_page_level_ads: true});</script></head>', $html);
    var_dump($mmc, '#LTRE1', $articleHtml, '#LTRE2', $html, '#LTRE3');
    var_dump($mmc->set('article_duowan_home', $html, 60*5));
    //文章页
    $articleHtml = $mmc->get('article_common_js_css');
    $html = file_get_contents('http://www.duowan.com/1705/m_359028129759.html');
    $html .= '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script><script>(adsbygoogle = window.adsbygoogle || []).push({google_ad_client: "ca-pub-7403881508152497", enable_page_level_ads: true});</script></head>';
    var_dump($mmc, '#LTRE4', $articleHtml, '#LTRE5', $html, '#LTRE6');
    var_dump($mmc->set('article_common_js_css', $html, 60*5));
    sleep(5);
}
    

