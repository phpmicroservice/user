# user_center
用户中心服务


### 项目目录结构

 |- _**app**_ 主要代码目录 \
 &nbsp;&nbsp;&nbsp;&nbsp;|- _**controller**_ 控制器 \
 &nbsp;&nbsp;&nbsp;&nbsp;|- _**filterTool**_ 过滤工具 \
 &nbsp;&nbsp;&nbsp;&nbsp;|- _**logic**_ 逻辑 \
 &nbsp;&nbsp;&nbsp;&nbsp;|- _**model**_ 模型 \
 &nbsp;&nbsp;&nbsp;&nbsp;|- _**processor**_ 处理器 \
 &nbsp;&nbsp;&nbsp;&nbsp;|- _**table**_ 共享内存表格 \
 &nbsp;&nbsp;&nbsp;&nbsp;|- _**task**_ task任务 \
 &nbsp;&nbsp;&nbsp;&nbsp;|- _**validation**_ 验证 \
 &nbsp;&nbsp;&nbsp;&nbsp;|- _**validator**_ 验证器 \
 &nbsp;&nbsp;&nbsp;&nbsp;|- _Alc.php_ **权限控制** \
 &nbsp;&nbsp;&nbsp;&nbsp;|- _Base.php_ **基类** \
 &nbsp;&nbsp;&nbsp;&nbsp;|- _Controller.php_ **控制器基类** \
 &nbsp;&nbsp;&nbsp;&nbsp;|- _di.php_ **依赖注入** \
 &nbsp;&nbsp;&nbsp;&nbsp;|- _Guidance.php_ **引导文件,初始化文件** \
 &nbsp;&nbsp;&nbsp;&nbsp;|- _NotFound.php_ **404处理** \
 |- _**config**_ 配置 \
 |- _**db**_ 数据库目录 \
 |- _**tool**_ 工具目录,存放一些小公举 \
 |- _**vendor**_ Composer扩展安装目录 \
 |- _**start**_ 启动文件目录 \
  &nbsp;&nbsp;&nbsp;&nbsp;|- _start.php_ **启动文件** \
 |- _**runtime**_ 运行临时文件 \
 &nbsp;&nbsp;&nbsp;&nbsp;|- _**cache**_ 缓存 \
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;∟ _**view**_ 视图缓存 \
 &nbsp;&nbsp;&nbsp;&nbsp;∟ _**logs**_ 日志 \
 |- . 根目录下的文件都有用,谨慎删除 \
 |- _Dockerfile_ **docker的构建文件** \
 |- _Composer.json_ **Composer的配置文件** \
 |- _Composer.lock_ **Composer的锁文件** \
 |- _.gitignore_ **Git的排除规则** \
 
 ### 环境变量列表
 
 |环境变量标识(全大写)|含义|默认值|
 |:---|:---:|---:|
 |APP_DEBUG|是否开启DEBUG|1|
 |APP_SECRET_KEY|秘钥||
 |NO_OUTPUT|不输出命令行西悉尼|1|
 |---|---|---|
 |MYSQL_HOST|mysql地址||
 |MYSQL_PORT|mysql的数据库名||
 |MYSQL_DBNAME|mysql的数据库名||
 |MYSQL_PASSWORD|mysql密码||
 |MYSQL_USERNAME|mysql用户名||
 |---|---|---|
 |SESSION_CACHE_PREFIX|session的redis储存的前缀||
 |SESSION_CACHE_HOST|session的redis储存的地址||
 |SESSION_CACHE_PORT|session的redis储存的端口|6379|
 |SESSION_CACHE_PERSISTENT|session的redis储存的persistent|false|
 |SESSION_CACHE_INDEX|session的redis储存的数据库索引|1|
 |SESSION_CACHE_AUTH|session的redis储存的权限验证|''|
 |---|---|---|
 |GCACHE_HOST|全局缓存的服务器地址||
 |GCACHE_PORT|全局缓存的端口||
 |GCACHE_AUTH|全局缓存的鉴权||
 |GCACHE_PERSISTENT|全局缓存的persistent||
 |GCACHE_PREFIX|全局缓存的前缀||
 |GCACHE_INDEX|全局缓存的库名||
 |---|---|---|
 |VALIDATION_APP_SECRET_KEY|验证服务的通讯秘钥||
 |EMAIL_APP_SECRET_KEY|邮件服务通讯秘钥|
 |RBAC_APP_SECRET_KEY|RBAC服务通讯密钥|
 |REGISTER_SECRET_KEY|服务注册通讯秘钥|
 
