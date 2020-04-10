<?php

/*************************************************
 *                  Easy PHP                     *
 *                                               *
 * A Faster Lightweight Full-Stack PHP apes *
 *                                               *
 *                  TIERGB                       *
 *        <https://github.com/TIGERB>            *
 *                                               *
 *************************************************/

namespace apes\Handles;

use apes\App;
use apes\Exceptions\CoreHttpException;
use ReflectionClass;
use Closure;
use apes\Router\Job;
use apes\Router\EasyRouter;

/**
 * 路由处理机制.
 *
 * @author TIERGB <https://github.com/TIGERB>
 */
class RouterHandle implements Handle
{
    /**
     * 注册路由处理机制.
     *
     * @param App $app 框架实例
     * @param void
     */
    public function register(App $app)
    {
        // 初始化路由模块入口类
        (new EasyRouter())->init($app);
    }
}
