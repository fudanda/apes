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

use apes\Handles\ErrorHandle;
use apes\Handles\ExceptionHandle;
use apes\Handles\EnvHandle;
use apes\Handles\RouterSwooleHandle;
use apes\Handles\ConfigHandle;
use apes\Handles\LogHandle;
use apes\Handles\NosqlHandle;
use apes\Handles\UserDefinedHandle;
use apes\Exceptions\CoreHttpException;
use apes\Request;
use apes\Response;

/**
 * 引入框架文件
 *
 * Require apes
 */
require(__DIR__ . '/App.php');

try {
    //------------------------------------------------------------------------//
    //                                  INIT                                  //
    //------------------------------------------------------------------------//

    /**
     * 初始化应用
     *
     * Init apes
     */
    $app = new apes\App(__DIR__ . '/..', function () {
        return require(__DIR__ . '/Load.php');
    });
    // set the app running mode
    $app->runningMode = 'cli';

    //-----------------------------------------------------------------------//
    //                         LOADING HANDLE MODULE                         //
    //-----------------------------------------------------------------------//

    /**
     * 挂载handles
     *
     * Load all kinds of handles
     */
    $app->load(function () {
        // 加载预环境参数机制 Loading env handle
        return new EnvHandle();
    });

    $app->load(function () {
        // 加载预定义配置机制 Loading config handle
        return new ConfigHandle();
    });

    $app->load(function () {
        // 加载日志处理机制 Loading log handle
        return new LogHandle();
    });

    $app->load(function () {
        // 加载错误处理机制 Loading error handle
        return new ErrorHandle();
    });

    $app->load(function () {
        //  加载异常处理机制 Loading exception handle.
        return new ExceptionHandle();
    });

    $app->load(function () {
        // 加载nosql机制 Loading nosql handle
        return new NosqlHandle();
    });

    $app->load(function () {
        // 加载用户自定义机制 Loading user-defined handle
        return new UserDefinedHandle();
    });

    $app->load(function () {
        // 加载路由机制 Loading route handle
        return new RouterSwooleHandle();
    });

    /**
     * 启动应用
     *
     * Start apes
     */
    $app->run(function () use ($app) {
        return new Request($app);
    });

    return $app;
} catch (CoreHttpException $e) {
    /**
     * 捕获异常
     *
     * Catch exception
     */
    $e->reponse();
}
