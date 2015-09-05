<?php
/**
 * User: zhuyajie
 * Date: 15-9-4
 * Time: 下午11:32
 */
namespace Snowair\Think;
use Snowair\Think\Phpunit\Response;

trait Controller
{
    /**
     * Ajax方式返回数据到客户端
     * @access protected
     *
     * @param mixed  $data        要返回的数据
     * @param String $type        AJAX返回数据格式
     * @param int    $json_option 传递给json_encode的option参数
     *
     * @throws Response
     */
    protected function ajaxReturn($data,$type='',$json_option=0) {
        if(empty($type)) $type  =   C('DEFAULT_AJAX_RETURN');
        switch (strtoupper($type)){
            case 'XML'  :
                // 返回xml格式数据
                headers_sent() || header('Content-Type:text/xml; charset=utf-8');
                $response = (xml_encode($data));
                break;
            case 'JSONP':
                // 返回JSON数据格式到客户端 包含状态信息
                headers_sent() || header('Content-Type:application/json; charset=utf-8');
                $handler  =   isset($_GET[C('VAR_JSONP_HANDLER')]) ? $_GET[C('VAR_JSONP_HANDLER')] : C('DEFAULT_JSONP_HANDLER');
                $response = ($handler.'('.json_encode($data,$json_option).');');
                break;
            case 'EVAL' :
                // 返回可执行的js脚本
                headers_sent() || header('Content-Type:text/html; charset=utf-8');
                $response = ($data);
                break;
            default:
                // 返回JSON数据格式到客户端 包含状态信息
                headers_sent() || header('Content-Type:application/json; charset=utf-8');
                $response = (json_encode($data,$json_option));
        }
        if (C('phpunit')) {
            throw new Response($response);
        }else{
            exit($response);
        }

    }

    /**
     * 默认跳转操作 支持错误导向和正确跳转
     * 调用模板显示 默认为public目录下面的success页面
     * 提示页面为可配置 支持模板标签
     *
     * @param string   $message 提示信息
     * @param bool|int $status  状态
     * @param string   $jumpUrl 页面跳转地址
     * @param mixed    $ajax    是否为Ajax方式 当数字时指定跳转时间
     *
     * @throws Response
     * @access private
     */
    private function dispatchJump($message,$status=1,$jumpUrl='',$ajax=false) {
        if(true === $ajax || IS_AJAX) {
            $data           =   is_array($ajax)?$ajax:array();
            $data['info']   =   $message;
            $data['status'] =   $status;
            $data['url']    =   $jumpUrl;
            $this->ajaxReturn($data);
        }
        if(is_int($ajax)) $this->assign('waitSecond',$ajax);
        if(!empty($jumpUrl)) $this->assign('jumpUrl',$jumpUrl);
        // 提示标题
        $this->assign('msgTitle',$status? L('_OPERATION_SUCCESS_') : L('_OPERATION_FAIL_'));
        //如果设置了关闭窗口，则提示完毕后自动关闭窗口
        if($this->get('closeWin'))    $this->assign('jumpUrl','javascript:window.close();');
        $this->assign('status',$status);   // 状态
        //保证输出不受静态缓存影响
        C('HTML_CACHE_ON',false);
        if($status) { //发送成功信息
            $this->assign('message',$message);// 提示信息
            // 成功操作后默认停留1秒
            if(!isset($this->waitSecond))    $this->assign('waitSecond','1');
            // 默认操作成功自动返回操作前页面
            if(!isset($this->jumpUrl)) $this->assign("jumpUrl",$_SERVER["HTTP_REFERER"]);
            $this->display(C('TMPL_ACTION_SUCCESS'));
        }else{
            ob_start();
            $this->assign('error',$message);// 提示信息
            //发生错误时候默认停留3秒
            if(!isset($this->waitSecond))    $this->assign('waitSecond','3');
            // 默认发生错误的话自动返回上页
            if(!isset($this->jumpUrl)) $this->assign('jumpUrl',"javascript:history.back(-1);");
            $this->display(C('TMPL_ACTION_ERROR'));
            // 中止执行  避免出错后继续执行
            $response= ob_get_clean();
            if (C('phpunit')) {
                throw new Response($response);
            }else{
                exit($response);
            }
        }
    }

    protected function error($message='',$jumpUrl='',$ajax=false) {
        $this->dispatchJump($message,0,$jumpUrl,$ajax);
    }

}
