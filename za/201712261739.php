#!/usr/local/php/bin/php
<?php

//查找影视订阅号追踪没有及时通知到的视频，全部都触发通知一次
//放到221.228.83.82机器执行


$GLOBALS = [
    'mysql' => [
        'MYSQL_HOST' => '10.20.167.211',
        'MYSQL_PORT' => '6304',
        'MYSQL_USER' => 'video_dw_rw',
        'MYSQL_DB' => 'video_dw',
        'MYSQL_PASS' => 'R02K,Th8K+DP',
        'MYSQL_CHARSET' => 'utf8',
    ],
];



class Model{
	public $page = null;
	public $sql = '';
	protected $table_name;
    protected $db_instance = "mysql";
	private static $_db = array();

    public function getDbInstance($instance){
        return $this->db_instance;
    }
    
    public function setDbInstance($instance){
        $this->db_instance = $instance;
    }

	public function select( $conditions=array(), $field='', $order=null, $limit=null ){
		$field = !empty($field) ? $field : '*';
		$order = !empty($order) ? ' ORDER BY '.$order : '';
		$conditions = $this->_where($conditions);

		$sql = ' FROM '.$this->table_name.$conditions["_where"];
		if(is_array($limit)){
			if(! $total = $this->query('SELECT COUNT(*) as dw_counter '.$sql, $conditions["_bindParams"]))return null;
			$limit = $limit + array(1, 10, 10);
			$limit = $this->pager($limit[0], $limit[1], $limit[2], $total[0]['dw_counter']);
			$limit = empty($limit) ? '' : ' LIMIT '.$limit['offset'].','.$limit['limit'];			
		}else{
			$limit = !empty($limit) ? ' LIMIT '.$limit : '';
		}
		return $this->query('SELECT '. $field . $sql . $order . $limit, $conditions["_bindParams"]);
	}
	
	public function find( $conditions=array(),$field='', $order=null ){
		$field = !empty($field) ? $field : '*';
		$res = $this->select($conditions, $field, $order,1);
		return !empty($res) ? array_pop($res) : false;
	}
	
	public function update( $conditions=array(),$new_data=array() ){
		$values = array();
		foreach ($new_data as $k=>$v){
			$values[":M_UPDATE_".$k] = $v;
			$setstr[] = $k."=".":M_UPDATE_".$k;
		}
		$conditions = $this->_where( $conditions );
		return $this->execute("UPDATE ".$this->table_name." SET ".implode(', ', $setstr).$conditions["_where"], $conditions["_bindParams"] + $values);
	}

	function incr($conditions=array(), $field, $optval = 1) {
		$conditions = $this->_where( $conditions );
		return $this->execute("UPDATE ".$this->table_name." SET `{$field}` = `{$field}` + :M_INCR_VAL ".$conditions["_where"], $conditions["_bindParams"] + array(":M_INCR_VAL" => $optval));
	}
	
	public function delete( $conditions=array() ){
		$conditions = $this->_where( $conditions );
		return $this->execute("DELETE FROM ".$this->table_name.$conditions["_where"], $conditions["_bindParams"]);
	}
	
	public function insert($data){
		$values = array();
		foreach($data as $k=>$v){
			$keys[] = $k; $values[":".$k] = $v; $marks[] = ":".$k;
		}
		$this->execute("INSERT INTO ".$this->table_name." (".implode(', ', $keys).") VALUES (".implode(', ', $marks).")", $values);
		return $this->_getDb()->lastInsertId();
	}
	
	public function count( $conditions=null ){
		$conditions = $this->_where( $conditions );
		$count = $this->query("SELECT COUNT(*) AS total FROM ".$this->table_name.$conditions["_where"], $conditions["_bindParams"]);
		return isset($count[0]['total']) && $count[0]['total'] ? $count[0]['total'] : 0;
	}
	
	public function query($sql=null, $params=array() ){
		$this->sql = $sql;
		$sth = $this->_bindParams( $sql, $params );
		if( $sth->execute() ) return $sth->fetchAll(PDO::FETCH_ASSOC);
		$err = $sth->errorInfo();
		throw new Exception('Database SQL: "' . $sql. '". ErrorInfo: '. $err[2], 1);
	}
	
	public function execute( $sql=null, $params=array() ){
		$this->sql = $sql;
		$sth = $this->_bindParams( $sql, $params );
		if( $sth->execute() ) return $sth->rowCount();
		$err = $sth->errorInfo();
		throw new Exception('Database SQL: "' . $sql. '". ErrorInfo: '. $err[2], 1);
	}

	public function escape($str){
        if(is_null($str))return 'null';
		if(is_bool($str))return $str ? 1 : 0;
		if(is_int($str))return (int)$str;
		if(@get_magic_quotes_gpc())$str = stripslashes($str);
		return $this->_getDb()->quote($str);
	}
	
	public function __construct($table_name = null, $db_instance = null){
		if( $table_name ) $this->table_name = $table_name;
        if( $db_instance ) $this->db_instance = $db_instance;
		//if( !is_object(Model::$_db) ) $this->_connect();
	}
	
	public function pager($page, $pageSize = 10, $scope = 10, $total)
	{
		$this->page = null;
		if($total > $pageSize){
			$total_page = ceil( $total / $pageSize );
			$page = min(intval(max($page, 1)), $total);
			$this->page = array(
				'total_count' => $total, 
				'page_size'   => $pageSize,
				'total_page'  => $total_page,
				'first_page'  => 1,
				'prev_page'   => ( ( 1 == $page ) ? 1 : ($page - 1) ),
				'next_page'   => ( ( $page == $total_page ) ? $total_page : ($page + 1)),
				'last_page'   => $total_page,
				'current_page'=> $page,
				'all_pages'   => array(),
				'offset'      => ($page - 1) * $pageSize,
				'limit'       => $pageSize,
			);
			$scope = (int)$scope;
			if($total_page <= $scope ){
				$this->page['all_pages'] = range(1, $total_page);
			}elseif( $page <= $scope/2) {
				$this->page['all_pages'] = range(1, $scope);
			}elseif( $page <= $total_page - $scope/2 ){
				$right = $page + (int)($scope/2);
				$this->page['all_pages'] = range($right-$scope+1, $right);
			}else{
				$this->page['all_pages'] = range($total_page-$scope+1, $total_page);
			}
		}
		return $this->page;
	}
	
    private function _getDb(){
        if( !is_object(@Model::$_db[$this->db_instance]) ) $this->_connect();
        return Model::$_db[$this->db_instance];
    }
    
	private function _connect(){
		if( !isset($GLOBALS[$this->db_instance]) || empty($GLOBALS[$this->db_instance]) ) {
            $this->db_instance = 'mysql';    
        }  
		Model::$_db[$this->db_instance] = new PDO('mysql:dbname='.$GLOBALS[$this->db_instance]['MYSQL_DB'].';host='.$GLOBALS[$this->db_instance]['MYSQL_HOST'].';port='.$GLOBALS[$this->db_instance]['MYSQL_PORT'], $GLOBALS[$this->db_instance]['MYSQL_USER'], $GLOBALS[$this->db_instance]['MYSQL_PASS'], array(PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES \''.$GLOBALS[$this->db_instance]['MYSQL_CHARSET'].'\''));
	}

	private function _bindParams($sql, $params=array()){
		$sth = $this->_getDb()->prepare($sql);
		if( is_array($params) && !empty($params) ){
			foreach($params as $k=>&$v){
				$sth->bindParam($k, $v);
			}		
		}
		return $sth;
	}
	
	private function _where( $conditions=array() ){
		$result = array( "_where" => " ","_bindParams" => array());
		if(is_array($conditions) && !empty($conditions)){
			$fields = array(); $sql = null; $join = array();
			if(isset($conditions[0]) && $sql = $conditions[0]) unset($conditions[0]);
			foreach( $conditions as $key => $condition ){
				if(substr($key, 0, 1) != ":"){
					unset($conditions[$key]);
					$conditions[":".$key] = $condition;
				}
				$join[] = "`{$key}` = :{$key}";
			}
			if(!$sql) $sql = join(" AND ",$join);

			$result["_where"] = " WHERE ". $sql;
			$result["_bindParams"] = $conditions;
		}
		return $result;
	}
}

/* $upM = new Model('upload_list', 'mysql');
foreach ([8641841,8641843,8641845,8641863,8641867,8651107,8651131,8670797,8670801,8670805,8670809,8670811] as $vid) {
    echo $vid.',';
    var_dump($upM->update(['vid' => $vid], ['letv_play' => 1]));
    var_dump($upM->update(['vid' => $vid], ['letv_play' => 0]));
}

die; */

$sql = "select vid from upload_list where 
        channel = 'yingshivideo' 
        and source_client = 45 
        and upload_start_time < unix_timestamp() - 3600*3 order by vid desc";
$upM = new Model('upload_list', 'mysql');
$list = $upM->query($sql) ?: [];
foreach ($list as $k => $v) {
    if ($k % 10 == 0) {
        sleep(1);
    }
    echo $v['vid'].", ";
    $upM->update(['vid' => $v['vid']], ['letv_play' => 1]);
    $upM->update(['vid' => $v['vid']], ['letv_play' => 0]);
}


