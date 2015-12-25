###introduce:
	php framework PI （目前本项目未经作者允许不能应用于其他项目，也不能基于本框架二次开发）

###depends:
	php >= 5.2.0
	webserver open path_info

###features:   
	1 轻量+插件化：核心功能支持核心业务逻辑，不同插件支持不同项目逻辑(如app.php支持com模块,web.php+路由插件支持web-mvc项目)
	2 强调代码管理：追求严格的代码格式控制
	3 支持远程调用：每个接口的方法都可以配置是否远程调用，服务器和客户端无需做任何调整
	

###usage:
	1   pi目录：核心框架 - 其下的web.php api.php task.php com.php 为常用的4各场景提供了一个示例
	2 proj目录：项目框架 - 其下的目录都是示例，可用作正式环境的参考

###todo list:
	1 (no) 队列 memcache+mongodb 引擎
	2 (ok) 梳理核心代码所有异常和错误梳理 - 该返回的返回，该报错的报错 （关注cache挂掉后，cache返回结果） 
	3 (ok) 梳理输入输出缓存模型
	4 (ok) 优化代码
	5 (no) 添加好的类库
	6 (no) 整理框架工具 + 文档
