<?php

/**
 * ThinkPHP 3.2.3 单元测试启动文件
 * zhuyajie
 * Date: 2015/7/2
 * Time: 16:56
 */
namespace Think;

class PhpunitHelper {

    protected static $_map=[];
    /**
     * @var  \Think\Model
     */
    protected $model;
    protected $testConfig=[];
    protected $action_name;

    /**
     * @param string $app_path
     * @param string $think_path
     * @param string $runtime_path 不要与正式的runtime目录相同
     */
    public function __construct($app_path, $think_path, $runtime_path)
    {
        if (!file_exists( $app_path )) {
            throw new \RuntimeException($app_path.'不存在');
        }

        if (!file_exists( $think_path )) {
            throw new \RuntimeException($think_path.'不存在');
        }

        if (!file_exists( $runtime_path )) {
            $bool = mkdir($runtime_path);
            if (!$bool) {
                throw new \RuntimeException($runtime_path.'创建失败');
            }
        }else if (!is_writable( $runtime_path )) {
            throw new \RuntimeException($runtime_path.'不可写');
        }

        define ( 'APP_DEBUG', true );
        define ( 'APP_PATH', rtrim($app_path,'\\/').'/' );
        define ( 'RUNTIME_PATH', rtrim($runtime_path,'\\/').'/' );
        define ( 'THINK_PATH',rtrim($think_path,'\\/').'/');
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REMOTE_ADDR']='127.0.0.1';
        $_SERVER['REMOTE_PORT']='32800';
        $_SERVER['SERVER_ADDR']='127.0.0.1';
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['HTTP_REFERER']='/';

        $this->_defineConsts();
    }

    /**
     * 模拟常规的mvc应用
     * @param        $http_host
     * @param        $module_name
     * @param        $controller_name
     * @param string $request_scheme
     * @param string $server_port
     */
    public function setMVC($http_host,$module_name,$controller_name,$request_scheme='http',$server_port='80')
    {
        $this->setServerEnv('SERVER_NAME',$http_host);
        $this->setServerEnv('HTTP_HOST',$http_host);
        $this->setServerEnv('REQUEST_SCHEME',$request_scheme);
        $this->setServerEnv('SERVER_PORT',$server_port);
        defined('MODULE_NAME') or define('MODULE_NAME', $module_name );
        defined('CONTROLLER_NAME') or define('CONTROLLER_NAME', $controller_name);
    }

    /**
     * 定义单个常量
     * @param $name
     * @param $value
     */
    public function defineConst( $name, $value )
    {
        defined($name) or define ( $name, $value );
    }

    /**
     * 批量定义常量
     * @param array $map
     *
     * @return $this
     */
    public function defineConsts( array $map )
    {
        foreach ($map as $name => $value) {
            defined($name) or define ( $name, $value );
        }
        return $this;
    }

    /**
     * 设置单个 $_SERVER 环境变量
     * @param $name
     * @param $value
     */
    public function setServerEnv( $name, $value )
    {
        $_SERVER[$name] = $value;
    }

    /**
     * 批量设置 $_SERVER 环境变量
     * @param array $map
     *
     * @return $this
     */
    public function setServerEnvs(array $map )
    {
        foreach ($map as $name => $value) {
            $_SERVER[$name] = $value;
        }
        return $this;
    }

    /**
     * 设置 $_GET 变量
     * @param $get
     */
    public function setGET( $get )
    {
        $_GET = $get;
        $_REQUEST=array_merge($_REQUEST,$_GET);
    }

    /**
     * 设置 $_POST变量
     * @param $post
     */
    public function setPOST( $post )
    {
        $_POST = $post;
        $_REQUEST=array_merge($_REQUEST,$_POST);
    }

    /**
     * 定义核心常量
     */
    private function _defineConsts() {

        $GLOBALS['_beginTime'] = microtime(TRUE);
        // 记录内存初始使用
        define('MEMORY_LIMIT_ON',function_exists('memory_get_usage'));
        if(MEMORY_LIMIT_ON) $GLOBALS['_startUseMems'] = memory_get_usage();

        define( 'THINK_VERSION', '3.2.3');

        if(function_exists('saeAutoLoader')){// 自动识别SAE环境
            defined('APP_MODE')     or define('APP_MODE',      'sae');
            defined('STORAGE_TYPE') or define('STORAGE_TYPE',  'Sae');
        }else{
            defined('APP_MODE')     or define('APP_MODE',       'common'); // 应用模式 默认为普通模式
            defined('STORAGE_TYPE') or define('STORAGE_TYPE',   'File'); // 存储类型 默认为File
        }

        // URL 模式定义
        define('URL_COMMON'  ,   0);  //普通模式
        define('URL_PATHINFO',   1);  //PATHINFO模式
        define('URL_REWRITE' ,   2);  //REWRITE模式
        define('URL_COMPAT'  ,   3);  // 兼容模式

        defined('EXT') or define('EXT','.class.php');

        // 系统常量定义
        defined('APP_PATH')     or define('APP_PATH',       dirname($_SERVER['SCRIPT_FILENAME']).'/');
        defined('APP_STATUS')   or define('APP_STATUS',     ''); // 应用状态 加载对应的配置文件
        defined('APP_DEBUG')    or define('APP_DEBUG',      true); // 是否调试模式

        defined('RUNTIME_PATH') or define('RUNTIME_PATH',   APP_PATH.'Runtime/');   // 系统运行时目录
        defined('LIB_PATH')     or define('LIB_PATH',       realpath(THINK_PATH.'Library').'/'); // 系统核心类库目录
        defined('CORE_PATH')    or define('CORE_PATH',      LIB_PATH.'Think/'); // Think类库目录
        defined('BEHAVIOR_PATH')or define('BEHAVIOR_PATH',  LIB_PATH.'Behavior/'); // 行为类库目录
        defined('MODE_PATH')    or define('MODE_PATH',      THINK_PATH.'Mode/'); // 系统应用模式目录
        defined('VENDOR_PATH')  or define('VENDOR_PATH',    LIB_PATH.'Vendor/'); // 第三方类库目录
        defined('COMMON_PATH')  or define('COMMON_PATH',    APP_PATH.'Common/'); // 应用公共目录
        defined('CONF_PATH')    or define('CONF_PATH',      COMMON_PATH.'Conf/'); // 应用配置目录
        defined('LANG_PATH')    or define('LANG_PATH',      COMMON_PATH.'Lang/'); // 应用语言目录
        defined('HTML_PATH')    or define('HTML_PATH',      APP_PATH.'Html/'); // 应用静态目录
        defined('LOG_PATH')     or define('LOG_PATH',       RUNTIME_PATH.'Logs/'); // 应用日志目录
        defined('TEMP_PATH')    or define('TEMP_PATH',      RUNTIME_PATH.'Temp/'); // 应用缓存目录
        defined('DATA_PATH')    or define('DATA_PATH',      RUNTIME_PATH.'Data/'); // 应用数据目录
        defined('CACHE_PATH')   or define('CACHE_PATH',     RUNTIME_PATH.'Cache/'); // 应用模板缓存目录
        defined('CONF_EXT')     or define('CONF_EXT',       '.php'); // 配置文件后缀
        defined('CONF_PARSE')   or define('CONF_PARSE',     '');    // 配置文件解析方法
        defined('ADDON_PATH')   or define('ADDON_PATH',     APP_PATH.'Addon');

        // 系统信息
        if(version_compare(PHP_VERSION,'5.4.0','<')) {
            ini_set('magic_quotes_runtime',0);
            define('MAGIC_QUOTES_GPC',get_magic_quotes_gpc()?True:False);
        }else{
            define('MAGIC_QUOTES_GPC',false);
        }

        defined('IS_CGI')   or define('IS_CGI',0);
        defined('IS_WIN')   or define('IS_WIN',strstr(PHP_OS, 'WIN') ? 1 : 0 );
        defined('IS_CLI')   or define('IS_CLI',0);

        if(!IS_CLI) {
            // 当前文件名
            if(!defined('_PHP_FILE_')) {
                if(IS_CGI) {
                    //CGI/FASTCGI模式下
                    $_temp  = explode('.php',$_SERVER['PHP_SELF']);
                    define('_PHP_FILE_', rtrim(str_replace($_SERVER['HTTP_HOST'],'',$_temp[0].'.php'),'/'));
                }else {
                    define('_PHP_FILE_', rtrim($_SERVER['SCRIPT_NAME'],'/'));
                }
            }

            if(!defined('__ROOT__')) {
                $_root  =   rtrim(dirname(_PHP_FILE_),'/');
                define('__ROOT__',  (($_root=='/' || $_root=='\\')?'':$_root));
            }
        }

    }

    /**
     * 启动模拟应用
     */
    public function start()
    {
        spl_autoload_register('\\Think\\PhpunitHelper::autoload');
        register_shutdown_function('\\Think\\PhpunitHelper::fatalError');
        Storage::connect(STORAGE_TYPE);

        $mode   =   include is_file(CONF_PATH.'core.php')?CONF_PATH.'core.php':MODE_PATH.APP_MODE.'.php';
        // 加载核心文件
        foreach ($mode['core'] as $file){
            if(is_file($file)) {
                if ( strpos( $file, 'Think/Controller.class.php' )!==false
                    ||strpos( $file, 'Think\\Controller.class.php' )!==false
                    ||strpos( $file, 'Think/View.class.php' )!==false
                    ||strpos( $file, 'Think\\View.class.php' )!==false
                ) {
                    // not include
                }else{
                    include_once $file;
                }
            }
        }
        // 加载应用模式配置文件
        foreach ($mode['config'] as $key=>$file){
            is_numeric($key)?C(load_config($file)):C($key,load_config($file));
        }

        // 读取当前应用模式对应的配置文件
        if('common' != APP_MODE && is_file(CONF_PATH.'config_'.APP_MODE.CONF_EXT)){
            C(load_config(CONF_PATH.'config_'.APP_MODE.CONF_EXT));
        }

        // 加载模式别名定义
        if(isset($mode['alias'])){
            self::addMap(is_array($mode['alias'])?$mode['alias']:include $mode['alias']);
        }

        // 加载应用别名定义文件
        if(is_file(CONF_PATH.'alias.php')){
            self::addMap(include CONF_PATH.'alias.php');
        }

        // 加载模式行为定义
        if(isset($mode['tags'])) {
            \Think\Hook::import(is_array($mode['tags'])?$mode['tags']:include $mode['tags']);
        }

        // 加载应用行为定义
        if(is_file(CONF_PATH.'tags.php')){
            // 允许应用增加开发模式配置定义
            \Think\Hook::import(include CONF_PATH.'tags.php');
        }

        // 加载框架底层语言包
        L(include THINK_PATH.'Lang/'.strtolower(C('DEFAULT_LANG')).'.php');
        // 调试模式加载系统默认的配置文件
        C(include THINK_PATH.'Conf/debug.php');
        // 读取应用调试配置文件
        if(is_file(CONF_PATH.'debug'.CONF_EXT)){
            C(include CONF_PATH.'debug'.CONF_EXT);
        }
        C('HTML_CACHE_ON',false);
        C('LIMIT_ROBOT_VISIT',false) ;
        C('LIMIT_PROXY_VISIT',false);
        $this->run();
    }

    protected function run()
    {
        \Think\Hook::listen('app_init');
        $this->init();
        // 应用开始标签
        \Think\Hook::listen('app_begin');

        $db_name = C('DB_NAME'); // 应用使用的数据库名

        C('SHOW_PAGE_TRACE',false);

        // 加载项目中定义的单元测试配置文件
        if (is_file(CONF_PATH.'test'.CONF_EXT)) {
            C(load_config(CONF_PATH.'test'.CONF_EXT));
        }
        // 加载.test.env文件定义的单元测试配置
        if (class_exists('\\Snowair\\Dotenv\\Loader')) {
            $this->loadEnvConfig();

        }

        $test_db_name = C('DB_NAME'); // 测试数据库的数据库名
        if ($db_name==$test_db_name) {
            C('DB_NAME','');
        }


        // 加载测试执行前setTestConfig方法临时设置的配置
        C($this->testConfig);

        $this->model = new \Think\Model();

        // 记录应用初始化时间
        G('initTime');
        // 应用结束标签
        \Think\Hook::listen('app_end');
    }

    protected function init()
    {

        load_ext_file(COMMON_PATH);

        // 日志目录转换为绝对路径 默认情况下存储到公共模块下面
        C('LOG_PATH',   realpath(LOG_PATH).'/Common/');

        // 定义当前请求的系统常量
        define('NOW_TIME',      $_SERVER['REQUEST_TIME']);
        define('REQUEST_METHOD',$_SERVER['REQUEST_METHOD']);
        define('IS_GET',        REQUEST_METHOD =='GET' ? true : false);
        define('IS_POST',       REQUEST_METHOD =='POST' ? true : false);
        define('IS_PUT',        REQUEST_METHOD =='PUT' ? true : false);
        define('IS_DELETE',     REQUEST_METHOD =='DELETE' ? true : false);

        // URL调度
        $this->dispatch();

        if(C('REQUEST_VARS_FILTER')){
            // 全局安全过滤
            array_walk_recursive($_GET,		'think_filter');
            array_walk_recursive($_POST,	'think_filter');
            array_walk_recursive($_REQUEST,	'think_filter');
        }

        // URL调度结束标签
        \Think\Hook::listen('url_dispatch');

        // TMPL_EXCEPTION_FILE 改为绝对地址
        C('TMPL_EXCEPTION_FILE',realpath(C('TMPL_EXCEPTION_FILE')));
        defined('IS_AJAX') or define(
            'IS_AJAX',
            ( (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
                || !empty($_POST[C('VAR_AJAX_SUBMIT')])
                || !empty($_GET[C('VAR_AJAX_SUBMIT')])
            ) ? true : false
        );
        return ;
    }

    /**
     * 设定控制器名称
     * @param $action
     */
    public function setActionName($action)
    {
        View::$action_name = $action;
        $this->action_name = $action;
    }

    protected function dispatch()
    {
        $urlCase        =   C('URL_CASE_INSENSITIVE');

        // 定义当前模块路径
        define('MODULE_PATH', APP_PATH.MODULE_NAME.'/');
        // 定义当前模块的模版缓存路径
        C('CACHE_PATH',CACHE_PATH.MODULE_NAME.'/');
        if (!file_exists(LOG_PATH)) {
            mkdir(LOG_PATH,0755);
        }
        C('LOG_PATH',  realpath(LOG_PATH).'/'.MODULE_NAME.'/');

        // 模块检测
        Hook::listen('module_check');

        // 加载模块配置文件
        if(is_file(MODULE_PATH.'Conf/config.php')){
            C(include MODULE_PATH.'Conf/config.php');
        }
        // 加载模块别名定义
        if(is_file(MODULE_PATH.'Conf/alias.php')){
            $map = include MODULE_PATH.'Conf/alias.php';
            foreach ($map as $class => $file) {
                include $file;
            }
        }
        // 加载模块函数文件
        if(is_file(MODULE_PATH.'Common/function.php')){
            include MODULE_PATH.'Common/function.php';
        }
        // 加载模块的扩展配置文件
        load_ext_file(MODULE_PATH);

        $depr = C('URL_PATHINFO_DEPR');
        define('MODULE_PATHINFO_DEPR',  $depr);

        if(!defined('__APP__')){
            $urlMode        =   C('URL_MODEL');
            if($urlMode == URL_COMPAT ){// 兼容模式判断
                $varPath        =   C('VAR_PATHINFO');
                define('PHP_FILE',_PHP_FILE_.'?'.$varPath.'=');
            }elseif($urlMode == URL_REWRITE ) {
                $url    =   dirname(_PHP_FILE_);
                if($url == '/' || $url == '\\')
                    $url    =   '';
                define('PHP_FILE',$url);
            }else {
                define('PHP_FILE',_PHP_FILE_);
            }
            // 当前应用地址
            define('__APP__',strip_tags(PHP_FILE));
        }

        $moduleName    =   MODULE_NAME;
        $controllerName =   CONTROLLER_NAME;
        define('__MODULE__',(defined('BIND_MODULE') || !C('MULTI_MODULE'))? __APP__ : __APP__.'/'.($urlCase ? strtolower($moduleName) : $moduleName));
        define('__CONTROLLER__',__MODULE__.$depr.(defined('BIND_CONTROLLER')? '': ( $urlCase ? parse_name($controllerName) : $controllerName )) );
        define('__ACTION__',__CONTROLLER__.$depr.$this->action_name);
        defined('__SELF__') || define('__SELF__',strip_tags(isset($_SERVER[C('URL_REQUEST_URI')])?$_SERVER[C('URL_REQUEST_URI')]:''));
    }

    /**
     * 无需手动调用
     * @param        $class
     * @param string $map
     */
    static public function addMap($class, $map=''){
        if(is_array($class)){
            self::$_map = array_merge(self::$_map, $class);
        }else{
            self::$_map[$class] = $map;
        }
    }

    /**
     * 启动加载器,已注册，无需手动调用
     * @param $class
     */
    public static function autoload($class)
    {
        if (isset(self::$_map[$class])) {
            include self::$_map[$class];
        }
    }

    static public function fatalError() {
        if ($e = error_get_last()) {
            print_r($e);
        }
    }

    /**
     * 返回默认模型
     * @return \Think\Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * 你可以直接在测试代码里设置用于覆盖默认配置的配置项
     * 但必须在start方法之前调用才有效
     * @param array $config
     */
    public function setTestConfig(array $config )
    {
        $config['phpunit']=true;
        $this->testConfig = $config;
    }

    /**
     * 如果composer安装了DotEnv,你可以使用.test.env文件配置
     */
    protected function loadEnvConfig()
    {
        $path =  dirname(APP_PATH);
        $env_file = $path.'/.test.env';
        if (file_exists($env_file)) {
            $Loader = new \Snowair\Dotenv\Loader($env_file);
            $Loader->setFilters(['Snowair\Dotenv\DotArrayFilter'])
                ->parse()
                ->filter();
            if( $expect=C('DOTENV.expect') ){
                call_user_func_array(array($Loader,'expect'),explode(',',$expect));
            }
            if(C('DOTENV.toConst')){
                $Loader->define();
            }
            if(C('DOTENV.toServer')){
                $Loader->toServer(true);
            }
            if(C('DOTENV.toEnv')){
                $Loader->toEnv(true);
            }
            $env = $Loader->toArray();
            C($env);
        };
    }
}


