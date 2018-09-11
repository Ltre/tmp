<?php
/**
 * 在一次程序运行的生命周期内，可支持的操作
 * 声明周期结束后，所有的数据都会清除
 */
class RuntimeLifeCycle {

    private static $dataPool = [];

    //备案的数据键，用于约束编码行为，方便其它开发人员
    private static $availableKeys = [
        'loginInfo', //当前管理员的登录信息
        'yyuid', //当前管理员的YYUID
    ];


    private function checkKey($key){
        if (! in_array($key, self::$availableKeys)) {
            //直接抛异常，上层不处理，要求开发人员必须在开发阶段解决该问题
            throw new Exception("此key[{$key}]未".__CLASS__."在备案");
        }
    }


    //保存整个程序在生命周期内可用的数据
    public function setData($key, $data){
        $this->checkKey($key);
        self::$dataPool[$key] = $data;
    }
    

    public function getData($key){
        $this->checkKey($key);
        $isset = isset(self::$dataPool[$key]);
        $data = $isset ? self::$dataPool[$key] : null;
        return [$isset, $data];
    }

}