<?php
class Fix{
    protected $mysqlInstance;
    protected $log = [];
    
    function __construct($instance = null){
        $this->mysqlInstance = $instance ?: 'mysql';
    }
    
    //入口
    public function fixData($tableNum=0){
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
        $initList = $this->getO2YInitListByPage($tableNum, $suffixNum, $page, $limit);
        if( empty($initList) ){
            return false;
        }
        $model = new Model("openid_to_yyuid_{$tableNum}", $this->mysqlInstance);
        foreach ($initList as $initData) {
            $o2yList = $model->select(['openid' => $initData['openid']]);
            $this->compareData($o2yList, $initData, $tableNum);
        }
        return true;        
    }


    function getO2YInitListByPage($tableNum=0, $suffixNum=0, $page=1, $limit=1000){
        $start = ($page-1)*$limit;
        $sql = "select * from openid_to_yyuid_init_{$tableNum} where yyuid like '%{$suffixNum}' order by openid asc limit {$start}, {$limit}";
        $model = new Model('openid_to_yyuid', $this->mysqlInstance);
        return $model->query($sql);
    }
    
    
    function compareData($o2yList, $initData, $tableNum) {
        //$found = false;//init.yyuid是否在o2y.status in (-1, 1)时存在匹配
        $map = [];//status => [yyuids..]
        foreach ($o2yList as $o2y) {
            if (in_array($o2y['status'], [1, -1])
                    && $o2y['type'] == $initData['type']
                    /* && $o2y['yyuid'] >= pow(2, 31) */ ) {
                $map[$o2y['status']][] = $o2y['yyuid'];
            }
        }
        $candidate = [];//YYUID候选表，低索引优先（目的为优先获取status=-1的最小yyuid）
        foreach ([-1, 1] as $status) {            
            if (isset($map[$status])) {
                $yyuids = $map[$status];
                sort($yyuids);
                $candidate[] = $yyuids[0];
            }
        }
        if (isset($candidate[0]) && $candidate[0] != $initData['yyuid']) {
            $model = new Model("openid_to_yyuid_init_{$tableNum}", $this->mysqlInstance);
            /* $model->update([
                'openid' => $initData['openid'],
                'type' => $initData['type'],
                'yyuid' => $initData['yyuid'],
                'appid' => $initData['appid'],
            ], ['yyuid' => $candidate[0]]); */
            echo "======================PARAMS======================\n";
            print_r(func_get_args());
            echo "======================MAP======================\n";
            print_r($map);
            echo "======================CANDIDATE======================\n";
            print_r($candidate);            
            echo "======================已处理======================\n";
            echo "YYUID: {$initData['yyuid']} -> {$candidate[0]} \n";
        } else {
            //echo "======================不需处理======================\n";
        }
    }
    
    
    //根据initList中的记录，查找对应o2y中有没有status=[-1,1]的记录。如有，则正常；否则为异常
    function compareData_0($o2yList, $initData){
        $found = false;
        foreach ($o2yList as $o2y) {
            if (in_array(intval($o2y['status']), [1, -1])
                    && $o2y['type'] == $initData['type'] 
                    && $o2y['yyuid'] == $initData['yyuid'] 
                    && $o2y['appid'] == $initData['appid']) {
                $found = true;
                break;
            }
        }
        if (! $found) {
            echo "-------------------NOT FOUND: -------------------\n";
            echo "initData is: \n";
            echo implode("\t", $initData);
            echo "o2y row is: \n";
            print_r($o2y);
            echo "\n";
        } else {
            echo "###################FOUND: ###################\n";
            echo "initData is: \n";
            echo implode("\t", $initData);
            echo "o2y row is: \n";
            print_r($o2y);
            echo "\n";
        }
    }

}
