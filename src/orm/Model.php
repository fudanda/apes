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

namespace apes\Orm;

use apes\App;
use apes\Orm\DB;
use apes\Exceptions\CoreHttpException;

/**
 *
 *
 * @author TIERGB <https://github.com/TIGERB>
 */
class Model extends DB
{
    /**
     * 构造函数
     *
     * __construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->getTableName();
    }

    /**
     * 获取表名
     *
     * get table name
     *
     * @return void
     */
    public function getTableName()
    {
        $prefix = App::$container->getSingle('config')
            ->config['database']['dbprefix'];
        $callClassName = get_called_class();
        $callClassName = explode('\\', $callClassName);
        $callClassName = array_pop($callClassName);
        if (!empty($this->tableName)) {
            if (empty($prefix)) {
                return;
            }
            $this->tableName = $prefix . '_' . $this->tableName;
            return;
        }
        preg_match_all('/([A-Z][a-z]*)/', $callClassName, $match);
        if (!isset($match[1][0]) || empty($match[1][0])) {
            throw new CoreHttpException('model name invalid', 401);
        }
        $match = $match[1];
        $count = count($match);
        if ($count === 1) {
            $this->tableName = strtolower($match[0]);
            if (empty($prefix)) {
                return;
            }
            $this->tableName = $prefix . '_' . $this->tableName;
            return;
        }
        $last = strtolower(array_pop($match));
        foreach ($match as $v) {
            $this->tableName .= strtolower($v) . '_';
        }
        $this->tableName .= $last;
        if (empty($prefix)) {
            return;
        }
        $this->tableName = $prefix . '_' . $this->tableName;
    }
}
