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

namespace apes\Traits;

/**
 * global constant manager
 */
trait GlobalConstant
{
    /**
     * register constant
     */
    public function registerGlobalConst()
    {
        define('NOW_TIME', time());
        define('NOW_MICROTIME', microtime(true));
    }
}
