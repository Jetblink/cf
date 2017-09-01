# cf
the Ctx php framework.

#### todo

//php 版本最低要求5.3
* 新增 validate 类
* 增加 nginx 配置范例
    * web,static,upload三种环境下的配置 
    * 静态文件采用独立的域名，上传文件夹是另一个域名，这两个域名，不做php解析，防止上传的东西被执行，同时为了方便做 cdn
    * nginx rewirte daxian.ren 指向 www.daxian.ren 静态域名和上传文件夹缓存相关配置 图片配置防盗链 防止用ip直接访问服务器

1. 图片目录不允许执行php这些，最多只能执行html，但是 静态又分两种，我们自己的(采用版本控制)和用户上传的(采用网站应用程序接受)
1. 设置404、favicon.ico
1. robots.txt、Sitemap.xml、
1. 错误日志收集，js错误，php错误, sql报错
