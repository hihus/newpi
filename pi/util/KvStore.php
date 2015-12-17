<?php

class KvStore{
    public static function set($key, $val){
        return NDb::get()->table('kv_store')->saveBody(array('`key`'=>$key,'`value`'=>$val))->insertOrUpdate();
    }

    /**
     * @param unknown_type $key
     * @return val
     */
    public static function get($key){
        NDb::get()->table('kv_store')->where(array('key'=>$key))->one();
    }

    /**
     * @param unknown_type $arr_key
     * @return array(key=>val, key=>val)
     */
    public static function bulk_get($arr_key){
        $hr = array();
        if(!is_array($arr_key) || empty($arr_key)){
            return $hr;
        }

        foreach ($arr_key as &$key){
            $key="'$key'";
        }

        $rs = NDB::get()->queryBySql(sprintf("select `key`, `value` from `kv_store` where `key` in (%s)", implode(',',$arr_key)));

        if(is_array($rs)){
            foreach($rs as $r){
                $hr[trim($r['key'])] = trim($r['value']);
            }
        }

        return $hr;
    }

    public static function existKey($key){
        $db = NDb::get();
        $result = $db->table('kv_store')->where(array('`key`'=>$key))->get();
        if(count($result) > 0){
            return true;
        }else{
            return false;
        }
    }
    public static function alter($key,$value){
        $db =NDB::get();
        $sql = "UPDATE `kv_store` SET `value` = ?  WHERE `key` = ?";
        return $db->queryBySql($sql,$value,$key);
    }
}
