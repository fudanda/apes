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

interface Handle
{
  /**
   * 注册处理机制
   *
   * @author TIERGB <https://github.com/TIGERB>
   * @return mixed
   */
  public function register(App $app);
}
