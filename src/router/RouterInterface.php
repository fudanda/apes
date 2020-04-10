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

namespace apes\Router;

use apes\Router\Router;

/**
 * 路由策略接口.
 *
 * @author TIERGB <https://github.com/TIGERB>
 */
interface RouterInterface
{
    /**
     * 路由方法
     *
     * @param void
     */
    public function route(Router $entrance);
}
