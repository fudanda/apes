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
 * nosql处理机制
 *
 * nosql handle
 *
 * @author TIERGB <https://github.com/TIGERB>
 */
class NosqlHandle implements Handle
{
    /**
     * 构造函数
     *
     * construct
     */
    public function __construct()
    {
        # code...
    }


    /**
     * 注册nosql处理机制
     *
     * register nosql handle
     *
     * @param  App    $app 框架实例 This apes instance
     * @return void
     */
    public function register(App $app)
    {
        $config = $app::$container->getSingle('config');
        if (empty($config->config['nosql'])) {
            return;
        }
        $config = explode(',', $config->config['nosql']);
        foreach ($config as $v) {
            $className = 'apes\Nosql\\' . ucfirst($v);
            App::$container->setSingle($v, function () use ($className) {
                // 懒加载　lazy load
                return $className::init();
            });
        }
    }
}
