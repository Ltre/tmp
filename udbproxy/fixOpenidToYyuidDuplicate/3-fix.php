<?php
class Fix{
    protected $model;
    protected $log = [];
    public function fixData($model, $tableNum=0){
        $this->model = $model;
        
        $this->fixDataByTable($tableNum, 10000);
        print_r($this->log);
    }
    
    function fixDataByTable($tableNum, $limit){
        for($suffixNum=0; $suffixNum<=9; $suffixNum++){
            $page = 1;
            while(1){
                $flag = $this->fixDataByPage($tableNum, $suffixNum, $page, $limit);
                if(false==$flag){
                    break;
                }
                $page++;
            }
        }
    }
    
    function fixDataByPage($tableNum, $suffixNum, $page, $limit){
        $list = $this->getO2YListByPage($tableNum, $suffixNum, $page, $limit);
        if( empty($list) ){
            return false;
        }
        foreach($list as $o2yRow){
            $y2oList = $this->getY2OListFromYYuid($o2yRow['yyuid']);
            $this->compareData($o2yRow, $y2oList);
        }
        return true;        
    }
    
    
    function getO2YListByPage($tableNum=0, $suffixNum=0, $page=1, $limit=1000){
        $start = ($page-1)*$limit;
        $sql = "select * from openid_to_yyuid_{$tableNum} where yyuid like '%{$suffixNum}' order by openid asc limit {$start}, {$limit}";
        return $this->model->query($sql);
    }

    function getY2OListFromYYuid($yyuid){
        $tableNum = $yyuid%10;
        $sql = "select * from yyuid_to_openid_$tableNum where yyuid={$yyuid} ";
        return $this->model->query($sql);   
    }
    
    function getO2YListFromOpenid($openid, $yyuid, $type, $status){
        $tableNum = ord(substr($openid, -1))%10;
        $sql = "select * from openid_to_yyuid_$tableNum where openid='{$openid}' and yyuid={$yyuid} and type='{$type}' and status={$status}";
        return $this->model->query($sql);   
    }
    
    function getO2YListFromOpenidOnly($openid){
        $tableNum = ord(substr($openid, -1))%10;
        $sql = "select * from openid_to_yyuid_$tableNum where openid='{$openid}'";
        return $this->model->query($sql);   
    }
    
    function compareData($o2yRow, $y2oList){
        unset($o2yRow['update_time']);
        
        foreach($y2oList as $v){
            if($o2yRow['openid']==$v['openid']){
                foreach($o2yRow as $field=>$vv){
                    //只要有1列不符合，那么就是不符合条件
                    if( $o2yRow[$field]!=$v[$field] ){
                        try {                            
                            $bkTableName = $this->model->table_name;
                            $this->model->table_name = $this->getTableByYYuid($o2yRow['yyuid']);
                            $updated = $this->model->update([ 'yyuid'=>$o2yRow['yyuid'], 'openid'=>$o2yRow['openid'] ], ['type'=>$o2yRow['type']]);
                            $this->model->table_name = $bkTableName;
                            $this->log['error'][] = "updated=[{$updated}]" . "error_type:{$field}\t".implode("\t", $o2yRow) ."##". implode("\t", $v);
                        } catch (Exception $e) {
                            echo "===========================Exception======================\n";
                            var_dump($e);
                            echo "\n";
                        }
                        break;
                    }
                }
            }else{
                $_list = $this->getO2YListFromOpenid($v['openid'], $v['yyuid'], $v['type'], $v['status']);
                //异常
                if( 1!=count($_list) ){
                    $this->log['exception'][] = implode("\t", $v)."@@".print_r($this->getO2YListFromOpenidOnly($v['openid']), 1);
                } 
                if (preg_match('/^5_/', $v['openid']) && 1 != count($_list)) {
                    $bkTableName = $this->model->table_name;
                    $this->model->update(['yyuid' => $v['yyuid'], 'openid' => $v['openid'], 'type' => $v['type'], 'appid' => $v['appid'], 'outer_id' => $v['outer_id']], ['status' => -3]);
                    $this->model->table_name = $bkTableName;
                    echo "To -3 status: \n";
                    echo json_encode($v);
                }
            }
        }
        
        //var_dump("o2yRow:", $o2yRow);
        //var_dump("y2oList:", $y2oList);
    }
    
    protected function getTableByYYuid($yyuid){
        $num = $yyuid%10;
        $table = 'yyuid_to_openid'.'_'.$num;
        return $table;
    }
}

//$model = "";
//(new Fix)->fixData($model);
