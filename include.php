<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

if (php_sapi_name()=='cli') {
    include_once __DIR__.DIRECTORY_SEPARATOR.'Think'.DIRECTORY_SEPARATOR.'Controller.php';
    include_once __DIR__.DIRECTORY_SEPARATOR.'Think'.DIRECTORY_SEPARATOR.'View.class.php';
    include_once __DIR__.DIRECTORY_SEPARATOR.'Think'.DIRECTORY_SEPARATOR.'PhpUnit.php';
}
