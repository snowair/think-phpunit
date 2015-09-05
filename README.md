# 说明

帮助ThinkPHP3.2.3(其他版本未测)项目实施phpunit单元测试.

这个文件是为了解决本人的实际问题而写，文件很简单，您看过以后完全可以自己继续完善或调整。

# 使用

以下为本人项目的实施过程。为了方便各种类的自动加载，项目使用comopser进行整合。

### 创建composer.json 并安装依赖

为了便于自动载人项目中的类，我将项目定义成了一个composer项目，即在项目根目录下面创建了项目的composer.json文件

```
{
  "name": "公司名/项目名",
  "autoload": {
    "classmap": ["Application","ThinkPHP/Library"]
  },
  "require-dev": {
    "snowair/think-phpunit": "dev-master"
  }
}
```

**关键在于： autoload和require-dev**

```
$ composer install --dev
```

然后**很重要的一项修改**: 删除 `ThinkPHP/ThinkPHP.php` 第 95 行:

```
require CORE_PATH.'Think'.EXT;
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

## 关于 header 函数

由于 phpunit 在启动后就已经产生了自己的输出, 所以被测的方法中不能直接使用 `header()` 函数, 否则会抛出错误. 

所以代码中使用了header函数的地方需要调整,有以下两种方式调整:

```
headers_sent() or header(''); // 推荐: 发送前判断是否已经产生过输出
@header(''); // 强制屏蔽错误
```

## 关于exit

由于exit 会中断一切执行, 所以 phpunit 无法测试使用了exit语句的方法 . 
 
如果你的方法中直接或间接用到了exit, 最佳的办法就是重构去除exit, 让程序能正常终结, 而不要强制终结.

### ajaxReturn/error方法

由于thinkphp的控制器的ajaxReturn方法和error方法使用了exit. 

所以对于测试使用了 ajaxReturn/error方法的控制器action, think-phpunit 提供了一种应对方案:


```
<?php
namespace Api\Controller;

use Think\Controller;

class TestController extends Controller
{
    public function t()
    {
        $this->ajaxReturn(['data'=>123]);
    }
}
```

单元测试方法这样写:

```
class TestControllerTest extends \PHPUnit_Framework_TestCase{

    static protected $app;

    static public function setupBeforeClass()
    {
        $base_path = __DIR__.'/../';
        self::$app = new \Think\PhpunitHelper($base_path.'Application/',$base_path.'ThinkPHP/',$base_path.'Runtime-test');
        self::$app->setMVC('domain.com','Api','Test');
        self::$app->setTestConfig(['DB_NAME'=>'test', 'DB_HOST'=>'127.0.0.1']);
        self::$app->start();
    }
    
    public function testT()
    {
        $controller = new \Api\Controller\TestController();
        try{
            self::$app->setActionName('t');
            $controller->t();
        }catch ( \Snowair\Think\Phpunit\Response $e){
            // Response异常对象保存了ajaxReturn/error的输出, 你只需要捕捉这个异常取出message.
            $this->assertEquals('{"data":123}',$e->getMessage());
        }
    }
}
```
