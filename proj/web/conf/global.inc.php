<?php

//全局web配置
//Conf::Set("hihu","hello");

Conf::set('global.view_lib_path','core/views/smarty-3.1.27/libs/Smarty.class.php');
Conf::set('global.view_engine','Smarty');
Conf::set('global.view_path',APP_ROOT.'view/views/');
//Conf::set('global.dispatcher_path',PI_CORE.'RouteDispatcher.php');
Conf::set('global.dispatcher_path',PI_CORE.'RouteDispatcher.php');
Conf::set('global.nolog_exception',array(1022=>1,1025=>1));