<?php

/********************************************
 *                Easy PHP                  *
 *                                          *
 * A lightweight PHP apes for studying *
 *                                          *
 *                 TIERGB                   *
 *      <https://github.com/TIGERB>         *
 *                                          *
 ********************************************/

namespace apes\Handles;

use apes\App;
use apes\Handles\Handle;
use apes\Exceptions\CoreHttpException;

/**
 * 错误处理机制
 *
 * @author TIERGB <https://github.com/TIGERB>
 */
class ErrorHandle implements Handle
{
    /**
     * 运行模式
     *
     * fpm/swoole
     * 
     * @var string
     */
    private $mode = 'fmp';

    /**
     * app instance
     *
     * @var apes\App
     */
    private $app = null;

    /**
     * 错误信息
     *
     * @var array
     */
    private $info = [];

    /**
     * 构造函数
     */
    public function __construct()
    {
        # code...
    }

    /**
     * 注册错误处理机制
     *
     * @param $app 框架实例
     *
     * @return void
     */
    public function register(App $app)
    {
        $this->mode = $app->runningMode;
        $this->app = $app;

        // do not report the error by php self
        // E_ERROR | E_WARNING | E_PARSE | E_NOTICE | E_ALL
        error_reporting(0);

        // report the by the set_error_handler&register_shutdown_function::error_get_last
        set_error_handler([$this, 'errorHandler']);
        register_shutdown_function([$this, 'shutdown']);
    }

    /**
     * 脚本结束
     *
     * @return　mixed
     */
    public function shutdown()
    {
        $error = error_get_last();
        if (empty($error)) {
            return;
        }
        $this->info = [
            'type'    => $error['type'],
            'message' => $error['message'],
            'file'    => $error['file'],
            'line'    => $error['line'],
        ];

        $this->end();
    }

    /**
     * 错误捕获
     *
     * @param  int    $errorNumber  错误码
     * @param  int    $errorMessage 错误信息
     * @param  string $errorFile    错误文件
     * @param  string $errorLine    错误行
     * @param  string $errorContext 错误文本
     * @return mixed               　
     */
    public function errorHandler(
        $errorNumber,
        $errorMessage,
        $errorFile,
        $errorLine,
        $errorContext
    ) {
        $this->info = [
            'type'    => $errorNumber,
            'message' => $errorMessage,
            'file'    => $errorFile,
            'line'    => $errorLine,
            'context' => $errorContext,
        ];

        $this->end();
    }

    /**
     * 脚本结束
     *
     * @return　mixed
     */
    private function end()
    {
        switch ($this->mode) {
            case 'swoole':
                CoreHttpException::reponseErrSwoole($this->info);
                break;

            default:
                CoreHttpException::reponseErr($this->info);
                break;
        }
    }
}
