# 说明

帮助ThinkPHP3.2项目实施phpunit单元测试.

# 使用

think-phpunit 是一个composer包, 需要首先安装composer, 本文不做介绍.

### 创建composer.json 并安装依赖

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

**关键在于： autoload和require-dev**, 你必须让composer能在测试时找到你的类.

```
$ composer install
```

安装好以后, 你就可以为项目中任何一个类创建单元测试类了:

### 创建单元测试类

think-phpunit的单元测试类遵循以下规则:

1. 继承自 `Think\Phpunit` 类
2. 每个类都要在 `setupBeforeClass` 方法中创建出模拟app实例.

测试文件组织比较自由，只要能保证phpunit能在执行时载入它即可. 我把测试文件放在了项目根目录下的 test 文件夹

对于TP项目而言, 最困难的地方在于控制器测试, think-phpunit 将一切简化到一行:

1. 使用控制器测试类的 `execAction($action_name)` 执行控制器action, 它会返回action执行产生的所有输出供你做断言.

下面是一个简单的测试控制器方法输出的示例测试文件,其他类型的类就不做介绍了:

```
<?php
namespace Home\Controller;

use Think\PhpUnit;

class IndexControllerTest extends PhpUnit
{

    static public function setupBeforeClass()
    {
        // 下面四行代码模拟出一个应用实例, 每一行都很关键, 需正确设置参数
        parent::$app = new \Think\PhpunitHelper();
        parent::$app->setMVC('gansubang.com','Home','Index');
        parent::$app->setTestConfig(['DB_NAME'=>'test', 'DB_HOST'=>'127.0.0.1',]); // 一定要设置一个测试用的数据库,避免测试过程破坏生产数据
        parent::$app->start();
    }

    /**
     * 控制器action输出测试示例
     */
    public function testIndex()
    {
        $output = $this->execAction('index');
        $this->assertEquals('hello world',$output);
    }
}
```

# 写出对测试友好的方法

要写出对测试友好的类, 只需要遵循两个简单的原则:

1. 不用使用 exit 语句, 因为他会终结一切, 包括测试.
2. 不要直接使用 header 函数, 因为phpunit输出在先.

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