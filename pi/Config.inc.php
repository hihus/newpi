<?php
//最低配置，几乎不修改
define("DOT",DIRECTORY_SEPARATOR);
define("TIMEZONE",'Asia/Shanghai');
define("ENCODE",'UTF-8');

define("PI_CORE",PI_ROOT.'core'.DOT);
define("PI_UTIl",PI_ROOT.'util'.DOT);
define("PI_PIPE",PI_ROOT.'pipe'.DOT);

//内部调用或者网络错误的返回err_code标识
define("INNER_ERR",'_pi_inner_err_code');
define("INNER_RES_PACK",'_pi_inner_content');

Pi::set('MUST_CONST',array('PI_ROOT','APP_ROOT','COM_ROOT'));
Pi::set('COM_DIR',array('export','lib','logic','model','conf'));
Pi::set('DefaultInputPipe','InputPipe');
Pi::set('DefaultOutputPipe','OutputPipe');
Pi::set('DbLib',PI_CORE.'db'.DOT.'db.php');
Pi::set('MemcacheLib',PI_CORE.'cache'.DOT.'Memcache.php');
Pi::set('RedisLib',PI_CORE.'cache'.DOT.'Redis.php');
Pi::set('LogLib',PI_CORE.'log'.DOT.'Log.php');
Pi::set('LoaderLib',PI_CORE.'Loader.php');
Pi::set('PipeExe',PI_CORE.'PipeExecutor.php');
Pi::set('PageCtr',PI_CORE.'PageCtr.php');

//inner api
Pi::set('global.innerapi_sign','kjsdgiu3kiusdf982o3sdfo034s');
Pi::set('global.innerapi_sign_name','_pi_inner_nm');

//其他配置

