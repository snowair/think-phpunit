<?php
/**
 * User: Administrator
 * Date: 2015/9/10
 * Time: 11:10
 */
namespace Think;

use Snowair\Think\Phpunit\Response;

abstract class PhpUnit extends \PHPUnit_Framework_TestCase
{

    /** @var  PhpunitHelper */
    protected static $app;
    /** @var  Controller */
    protected static $controller;

    static public function setController( $controller )
    {
        self::$controller=$controller;
    }

    /**
     * @param string $name   控制器方法名
     * @param array  $params 控制器参数列表
     *
     * @return mixed
     * @throws \Exception
     */
    public static function execAction($name,$params=array())
    {
        if (method_exists(self::$controller,$name)) {
            self::$app->setActionName($name);
            try{
                ob_start();
                call_user_func_array(array(self::$controller,$name),$params);
                return ob_get_clean();
            }catch (Response $e){
                ob_end_clean();
                return $e->getMessage();
            }
        }else{
            throw new \Exception($name.'方法不存在');
        }

    }

}