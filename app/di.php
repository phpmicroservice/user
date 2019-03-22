<?php

use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;

//注册自动加载
$loader = new \Phalcon\Loader();
$loader->registerNamespaces(
    [
        'app' => ROOT_DIR . '/app/',
    ]
);
$loader->register();



/**
 * The FactoryDefault Dependency Injector automatically registers the right
 * services to provide a full stack framework.
 */
$di = new Phalcon\DI\FactoryDefault\Cli();

$di->setShared('dispatcher', function () {
    #
    $dispatcher = new Phalcon\Cli\Dispatcher();
    $dispatcher->setDefaultNamespace('app\controller');
    $dispatcher->setActionSuffix('');
    $dispatcher->setTaskSuffix('');
    return $dispatcher;
});
$di->setShared('dConfig', function () {
    #Read configuration
    $config = new Phalcon\Config(require ROOT_DIR . '/config/config.php');
    return $config;
});

$di->setShared('config', function () {
    #Read configuration
    $config = new Phalcon\Config([]);
    return $config;
});

/**
 * 本地缓存
 */
$di->setShared('cache', function () {
    // Create an Output frontend. Cache the files for 2 days
    $frontCache = new \Phalcon\Cache\Frontend\Data(
        [
            "lifetime" => 172800,
        ]
    );

    $cache = new \Phalcon\Cache\Backend\File(
        $frontCache, [
            "cacheDir" => CACHE_DIR,
        ]
    );
    return $cache;
});

/**
 * 全局缓存
 */
$di->setShared('gCache', function () use ($di) {
    // Create an Output frontend. Cache the files for 2 days
    $frontCache = new \Phalcon\Cache\Frontend\Data(
        [
            "lifetime" => 172800,
        ]
    );
    $op = [
        "host" => getenv('GCACHE_HOST'),
        "port" => getenv('GCACHE_PORT'),
        "auth" => getenv('GCACHE_AUTH'),
        "persistent" => getenv('GCACHE_PERSISTENT'),
        'prefix' => getenv('GCACHE_PREFIX'),
    ];
    if (empty($op['auth'])) {
        unset($op['auth']);
    }
    $cache = new \Phalcon\Cache\Backend\Libmemcached($frontCache, [
        "servers" => [
            [
                "host" => $op['host'],
                "port" => $op['port'],
                "weight" => 1,
            ],
        ],
        "client" => [
            \Memcached::OPT_HASH => \Memcached::HASH_MD5,
            \Memcached::OPT_PREFIX_KEY => $op['prefix'],
        ],
    ]);
    return $cache;
});


/**
 * session缓存
 */
$di->setShared('sessionCache', function () use ($di) {
    // Create an Output frontend. Cache the files for 2 days
    $frontCache = new \Phalcon\Cache\Frontend\Data(
        [
            "lifetime" => 172800,
        ]
    );
    \pms\output($di['config']->cache, 'gCache');
    $op = [
        "host" => getenv('SESSION_CACHE_HOST'),
        "port" => \pms\get_env('SESSION_CACHE_PORT', 6379),
        "auth" => \pms\get_env('SESSION_CACHE_AUTH', ''),
        "persistent" => \pms\get_env('SESSION_CACHE_PERSISTENT', 1),
        'prefix' => \pms\get_env('SESSION_CACHE_PREFIX', 'session_'),
        "index" => getenv('SESSION_CACHE_INDEX')
    ];
    if (empty($op['auth'])) {
        unset($op['auth']);
    }
    $cache = new \Phalcon\Cache\Backend\Redis(
        $frontCache, $op);
    return $cache;
});




//注册过滤器,添加了几个自定义过滤方法
$di->setShared('filter', function () {
    $filter = new \Phalcon\Filter();
//    $filter->add('json', new \core\Filter\JsonFilter());
    return $filter;
});
//事件管理器
$di->setShared('eventsManager', function () {
    $eventsManager = new \Phalcon\Events\Manager();
    return $eventsManager;
});

//注册过滤器,添加了几个自定义过滤方法
$di->setShared('filter', function () {
    $filter = new \Phalcon\Filter();
//    $filter->add('json', new \core\Filter\JsonFilter());
    return $filter;
});


$di->set(
    "modelsManager", function () {
    return new \Phalcon\Mvc\Model\Manager();
});

$di->set(
    "proxyCS", function () {
    $client = new \pms\bear\ClientSync(\pms\get_env('PROXY_HOST'), \pms\get_env('PROXY_PROT'), 10);
    return $client;

});



$di->set('logger', function () {
    $config = array(
        'appenders' => array(
            'default' => array(
                'class' => 'LoggerAppenderPDO',
                'params' => array(
                    'dsn' => 'mysql:host=' . \pms\get_env('LOGPHP_PDO_HOST', \pms\get_env('MYSQL_HOST')) . ';dbname=' . \pms\get_env('LOGPHP_PDO_DBNAME', \pms\get_env('MYSQL_DBNAME')),
                    'user' => \pms\get_env('LOGPHP_PDO_USER', \pms\get_env('MYSQL_USERNAME')),
                    'password' => \pms\get_env('LOGPHP_PDO_PASSWORD', \pms\get_env('MYSQL_PASSWORD')),
                    'table' => 'web_log',
                    'insertSql' => "INSERT INTO __TABLE__ (timestamp, sname,logger,ipad, level, message, thread, file, line) VALUES (?,?, ?,?, ?, ?, ?, ?, ?)",
                    'insertPattern' => '%date{Y-m-d H:i:s},%e{SERVICE_NAME},%logger,%e{SERVER_ADDR},%level,%message,%pid,%file,%line'
                )
            ),
        ),
        'rootLogger' => array(
            'appenders' => array('default'),
        ),
    );
    \Logger::configure($config);
    return Logger::getLogger('default');

});


/**
 * Database connection is created based in the parameters defined in the
 * configuration file
 */
$di["db"] = function () use ($di) {
    return new DbAdapter(
        [
            "host" => getenv('MYSQL_HOST'),
            "port" => getenv('MYSQL_PORT'),
            "username" => getenv('MYSQL_USERNAME'),
            "password" => getenv('MYSQL_PASSWORD'),
            "dbname" => getenv('MYSQL_DBNAME'),
            'persistent'=>true,
            "options" => [
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
                \PDO::ATTR_CASE => \PDO::CASE_LOWER,
            ],
        ]
    );
};





$di->setShared('router2', function () {
    $router = new \Phalcon\Mvc\Router();
    $router->setDefaultController('open');
    $router->setDefaultAction('index');
    $router->add(
        "/:controller/:action/:params",
        [
            "controller" => 1,
            "action" => 2
        ]
    );
    $router->add(
        "/open",
        [
            "controller" => 'open',
            "action" => 'index'
        ]
    );


    $router->add(
        "/close",
        [
            "controller" => 'close',
            "action" => 'index'
        ]
    );
   
    
    return $router;
});


