<?php
//include './logo.php';
use \pms\TcpServer;
echo "开始主程序! \n";
define("SERVICE_NAME", "user");# 设置服务名字
define('ROOT_DIR', dirname(__DIR__));
require ROOT_DIR . '/vendor/autoload.php';
# 进行一些项目配置
define('APP_SECRET_KEY', \pms\get_env("APP_SECRET_KEY"));
define('DI_FILE', ROOT_DIR.'/app/di.php');
define('RUNTIME_DIR', ROOT_DIR.'/runtime/');

define('CACHE_DIR', RUNTIME_DIR.'/cache/');

$re9 = \pms\env_exist([
    'GCACHE_HOST', 'GCACHE_PORT', 'GCACHE_AUTH', 'GCACHE_PERSISTENT', 'GCACHE_PREFIX', 'GCACHE_INDEX',
    'MYSQL_HOST', 'MYSQL_PORT', 'MYSQL_DBNAME', 'MYSQL_PASSWORD', 'MYSQL_USERNAME']);
if (is_string($re9)) {
    exit('defined :' . $re9);
}


//注册自动加载
$loader = new \Phalcon\Loader();
$loader->registerNamespaces(
    [
        'app' => ROOT_DIR . '/app/',
        'tool' => ROOT_DIR . '/tool/',
        'regreg' => ROOT_DIR . '/regreg/',
    ]
);
$loader->register();

$server = new TcpServer('0.0.0.0', 9502, SWOOLE_PROCESS, SWOOLE_SOCK_TCP, [
    'daemonize' => false,
    'reload_async' => false,
    'reactor_num_mulriple' => 1,
    'worker_num_mulriple' => 1,
    'task_worker_num_mulriple' => 1
]);

$guidance = new \app\Guidance();
$server->onBind('onWorkerStart', $guidance);
$server->onBind('beforeStart', $guidance);
$server->onBind('onStart', $guidance);
$server->start();
