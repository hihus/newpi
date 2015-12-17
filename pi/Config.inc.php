<?php
//最低配置，几乎不修改
define("DOT",DIRECTORY_SEPARATOR);
define("TIMEZONE",'Asia/Shanghai');
define("ENCODE",'UTF-8');

define("PI_CORE",PI_ROOT.'core'.DOT);
define("PI_UTIl",PI_ROOT.'util'.DOT);
define("PI_PIPE",PI_ROOT.'pipe'.DOT);

Pi::set('MUST_CONST',array('PI_ROOT','APP_ROOT','COM_ROOT'));
Pi::set('COM_DIR',array('export','lib','logic','model','conf'));
Pi::set('DefaultInputPipe','InputPipe');
Pi::set('DefaultOutputPipe','OutputPipe');
Pi::set('DbLib',PI_CORE.'Db'.DOT.'medoo.php');
Pi::set('MemcacheLib',PI_CORE.'Cache'.DOT.'Memcache.php');
Pi::set('RedisLib',PI_CORE.'Cache'.DOT.'Redis.php');
Pi::set('LogLib',PI_CORE.'Log.php');
Pi::set('LoaderLib',PI_CORE.'Loader.php');
Pi::set('PipeExe',PI_CORE.'PipeExecutor.php');
Pi::set('PageCtr',PI_CORE.'PageCtr.php');

//其他配置