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
    "phpunit/phpunit": "^4.7",
    "snowair/phpunithelper-thinkphp": "dev-master"
  }
}
```

关键在于： autoload和require-dev

```
$ composer install --dev
```

### 创建单元测试文件

测试文件组织比较自由，我把测试文件放在了项目根目录下的 test 文件夹

下面是一个简单的示例测试文件 ApiTest.php

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
        self::$app->setMVC('test.com','Api','Pay','index');  
        // 设置测试环境使用的配置
        self::$app->setTestConfig(['DB_NAME'=>'test_poscard',
                                   'DB_HOST'=>'127.0.0.1',
                                   'phpunit'=>true]);
        // 启动模拟app
        self::$app->start();
    }

    public function setUp()
    {
        $this->api= new \Api\Controller\Api();
    }
    
    public function testIndex()
    {
        $json = $this->api->index();
        $this->assertNotEmpty($json,'没有任何输出');
        $array = json_decode($json,true);
        $this->assertNotEmpty($array,'没有任何数据');
        $this->assertTrue( isset($array['errcode']) , '没有errcode' );
        $this->assertTrue( isset($array['errmsg']) , '没有errmsg' );
        $this->assertEquals('0',$array['errcode'],'出错了：'.$array['errmsg']);
    }
}
```

