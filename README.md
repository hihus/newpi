###introduce:
	php framework PI

###depend:
	php >= 5.2.0

###features:
	1       插件化：核心功能支持核心业务逻辑，不同插件支持不同项目逻辑
	2  强调代码管理：追求严格的代码格式控制
	3  支持远程调用：每个接口的方法都可以配置是否远程调用，服务器和客户端无需做任何调整
	4  轻量

###todo list:
	1 队列 memcache+mongodb 引擎
	2 梳理核心代码所有异常和错误梳理 - 该返回的返回，该报错的报错 （关注cache挂掉后，cache返回结果）
	3 梳理输入输出缓存模型
	4 优化代码
	5 添加好的类库
	6 整理框架工具 + 文档
