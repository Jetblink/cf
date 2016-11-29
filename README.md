# cf
the Ctx php framework.

#### todo

//php 版本最低要求5.3
* 新增 Response validate 类,controller基类
* 中间件采用类似laravel的方式，url 为 requst 里边的 module/controller/action 来进行匹配
* 增加 nginx 配置范例
    * web,static,upload三种环境下的配置 
    * 静态文件采用独立的域名，上传文件夹是另一个域名，这两个域名，不做php解析，防止上传的东西被执行，同时为了方便做 cdn
    * nginx rewirte daxian.ren 指向 www.daxian.ren 静态域名和上传文件夹缓存相关配置 图片配置防盗链 防止用ip直接访问服务器


=== 待整理 ===
# 框架

1. 主要思想

    * 技术选型 centos  + nginx + php + mysql + redis + mongodb + 定时任务
    * session 采用 缓存 存放 （配置决定）
    * 常见的安全处理
    * 支持 api 请求方式，支持数据加密
    * 统计
    * 网站进入维护状态等
    * 常见的功能，注册登录 安装，后台管理界面基础界面，其它可选 （uv/pv统计）
    * 小工具 tool.php，包括 api调试 | mysql 操作 | 
    * 常见定时任务脚本，服务器登录发邮件 svn 自动更新 git 自动部署

###网站最佳实践:
1. 目录权限
1. web目录中只有index.php,其他的文件在外部，这样就不用怕文件遍历等
1. 主从
1. 图片的解决方案
1. 采用云存储(七牛，又拍云)
1. (
1. 自己设置独立的静态文件服务器：
1. 网页动静分离，缓存，上传文件夹和静态文件夹都不允许执行php等
1. 图片目录不允许执行php这些，最多只能执行html，但是 静态又分两种，我们自己的(采用版本控制)和用户上传的(采用网站应用程序接受)
1. 用户上传的需要进行过滤，而且目录需要写权限
1. html和php执行权限禁用
1. 图片配置防盗链
1. 
1. )
1. 设置404、favicon.ico
1. 
1. robots.txt、Sitemap.xml、
1. 错误日志收集，js错误，php错误, sql报错
1. 采用composer的方式组织，这样升级框架不会涉及到业务代码 也方便框架获取到最新的安全更新
1. 框架支持两种模式的配置：一般配置，敏感配置，一般配置在版本库中，敏感配置不在版本库中

