<?php
Pi::inc(dirname(__FILE__).DOT.'PICacheAbstract.php');
class Mem extends PICacheAbstract{
    private static $instance = array();
    
    public static function get($name){
        if(!is_string($name)){
            return null;
        }

        if(isset(self::$instance[$name])){
            return self::$instance[$name];
        }

        self::$instance[$name] = null;
        $conf = self::getConfig($name);
        if($conf == null) return null;
        self::$instance[$name] = new MemInner($conf);
        return self::$instance[$name];
    }

    public static function getConfig($name){
        $conf = Pi::get('cache.'.$name,array());
        if(empty($conf)) return null;
        foreach($conf as $server){
            if(!isset($server['host']) || !isset($server['host']) || !isset($server['unit']) ||
               !isset($server['port']) || !isset($server['pconnect']
            )){
                return null;
            }
        }
        return $conf;
    }
   
//end of class
}

class MemInner extends PICacheAbstract{
    public function __construct($conf){
        if(!is_array($conf) || empty($conf)){
            return null;
        }

        $this->conn = new Memcache();
        
        foreach ($conf as $server) {
            call_user_func_array(array($this->conn, 'addServer'), $server);
        }
    }

    /**
    * @param string $key
    * @param mixed $value
    * @param int $ttl
    * @return boolean
    */
    public function set($id, $data, $ttl = null){
        if (null === $ttl) {
            $ttl = $this->options['ttl'];
        }
        return $this->conn->set($id, $data, empty($this->options['compressed']) ? 0 : MEMCACHE_COMPRESSED, $ttl);
    }
}



