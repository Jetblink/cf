# cf
the Ctx php framework.

#### todo

//php 版本最低要求5.6
* 新增 Log Response validate 类,controller基类
* 异常处理(区分测试环境和生产环境)
* 中间件采用类似laravel的方式，url 为 requst 里边的 module/controller/action 来进行匹配
* 通过中间件控制或其他方式访问的路由需要权限auth，通过中间件记录http请求日志和http响应
* 补全contract，框架依赖的应用级别的类的约定
* Helpers/*
* 分页，验证码等基础类
* csrf中间件

