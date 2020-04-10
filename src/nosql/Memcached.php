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

namespace apes\Nosql;

use apes\App;
use Memcached as rootMemcached;

/**
 * memcached操作类
 *
 * @author TIERGB <https://github.com/TIGERB>
 */
class Memcached
{
    /**
     * 初始化
     *
     * init
     */
    public static function init()
    {
        $config = App::$container->getSingle('config');
        $config = $config->config['memcached'];
        $memcached  = new rootMemcached();
        $memcached->addServer($config['host'], $config['port']);
        return $memcached;
    }
}
