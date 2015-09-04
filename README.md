# 说明

帮助ThinkPHP3.2.3(其他版本未测)项目实施phpunit单元测试.

这个文件是为了解决本人的实际问题而写，文件很简单，您看过以后完全可以自己继续完善或调整。

# 使用

以下为本人项目的实施过程。为了方便各种类的自动加载，项目使用comopser进行整合。

### 创建composer.json 并安装依赖

为了便于自动载人项目中的类，我将项目定义成了一个composer项目，即在项目根目录下面创建了项目的composer.json文件

```
{
  "name": "snowair/service",
  "autoload": {
    "classmap": ["Application","ThinkPHP/Library"]
  },
  "require-dev": {
    "snowair/think-phpunit": "dev-master"
  }
}
```

** 关键在于： autoload和require-dev **

```
$ composer install --dev
```

### 创建单元测试文件

测试文件组织比较自由，我把测试文件放在了项目根目录下的 test 文件夹

下面是一个简单的测试控制器方法输出的示例测试文件 ApiTest.php

```
<?php
class ApiTest extends PHPUnit_Framework_TestCase {
    static protected $app;
    public $api;

    static public function setupBeforeClass()
    {
        $base_path = __DIR__.'/../';
        // 创建测试辅助实例
        self::$app = new \Think\PhpunitHelper($base_path.'Application/',$base_path.'ThinkPHP/',$base_path.'Runtime-test');
        // 模拟app
        self::$app->setMVC('test.com','Api','Pay');
        // 设置测试环境使用的配置
        self::$app->setTestConfig(['DB_NAME'=>'test_poscard',
                                   'DB_HOST'=>'127.0.0.1',
                                   'phpunit'=>true]);
        // 启动模拟app
        self::$app->start();
    }

    public function setUp()
    {
        $this->api= new \Api\Controller\ApiController;
    }
    
    /**
     * 测试字符串的输出
     */
    public function testOutput()
    {
        self::$app->defineConst('ACTION_NAME','output'); // 测试方法里先定义一些你的action方法里用到的常量
        $this->expectOutputString('123');
        $this->api->output();
    }
    
    /**
     * 测试json字符串的输出
     */
    public function testJsonOutput()
    {
        $that=$this;
        $this->setOutputCallback(function($out) use($that){
            $out = json_decode($out,true);
            $that->assertInternalType('array',$out);
            $that->assertArrayHasKey('status',$out);
            $that->assertEquals('0',$out['status']);
        });
        $this->api->jsonOutput();
    }
}
```

### 特别说明

1. 由于 控制器的 dispatchJump 方法和 ajaxReturn 方法中含有 exit, 而 exit 会中断一切执行, 所以phpunit无法测试使用了这两个方法(error方法,以及其他所有含有exit)的方法
    * 你需要调整代码, 去除代码中的exit, 否则无法测试它们.

2. 由于 phpunit 在启动后就已经产生了自己的输出, 所以被测的方法中不能直接使用 `header()` 函数, 否则会抛出错误. 所以代码中使用了header函数的地方需要调整,有以下两种方式调整:
    ```
    @header(''); // 强制屏蔽错误
    headers_sent() or header(''); // 发送前判断是否已经产生过输出
    ```
    * 对于ThinkPHP来说, 只需要修改ajaxReturn方法

