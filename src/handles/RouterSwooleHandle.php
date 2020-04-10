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
use Closure;
use apes\Router\EasySwooleRouter;

/**
 * 路由处理机制.
 *
 * @author TIERGB <https://github.com/TIGERB>
 */
class RouterSwooleHandle implements Handle
{
    /**
     * 注册路由处理机制.
     *
     * @param App $app 框架实例
     * @param void
     */
    public function register(App $app)
    {
        App::$container->set('router', function () {
            return new EasySwooleRouter();
        });
    }
}
