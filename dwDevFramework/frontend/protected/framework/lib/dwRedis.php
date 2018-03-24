<?php
class dwRedis extends Redis
{
	public function __construct($host = null, $port = null)
	{
        $host = (null == $host) ? $GLOBALS['redis']['host'] : $host;
        $port = (null == $port) ? $GLOBALS['redis']['port'] : $port;
        parent::connect( $host, $port );
        if (isset($GLOBALS['redis']['auth']) || ! empty($GLOBALS['redis']['auth'])) {
            parent::auth($GLOBALS['redis']['auth']);
        }
	}
}
?>