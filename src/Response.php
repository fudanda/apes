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

namespace apes;

use apes\App;

/**
 * 响应
 *
 * @author TIERGB <https://github.com/TIGERB>
 */
class Response
{

    /**
     * app instance
     *
     * @var apes\App
     */
    private $app = null;

    /**
     * 构造函数
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * 响应
     *
     * @param  mixed $response 响应内容
     * @return json
     */
    public function response($response)
    {
        header('Content-Type:Application/json; Charset=utf-8');
        die(json_encode(
            $response,
            JSON_UNESCAPED_UNICODE
        ));
    }

    /**
     * REST风格 成功响应
     *
     * @param  mixed $response 响应内容
     * @return json
     */
    public function restSuccess($response)
    {
        header('Content-Type:Application/json; Charset=utf-8');
        die(json_encode([
            'code'    => 200,
            'message' => 'OK',
            'result'  => $response
        ], JSON_UNESCAPED_UNICODE));
    }

    /**
     * cli模式成功响应
     *
     * @param  mixed $response 响应内容
     * @return array
     */
    public function cliModeSuccess($response)
    {
        var_dump([
            'code'    => 200,
            'message' => 'OK',
            'result'  => $response
        ]);
    }

    /**
     * REST风格 失败响应
     *
     * @param  mixed $response 响应内容
     * @return json
     */
    public function restFail(
        $code = 500,
        $message = 'Internet Server Error',
        $response
    ) {
        header('Content-Type:Application/json; Charset=utf-8');
        die(json_encode([
            'code'    => $code,
            'message' => $message,
            'result'  => $response
        ], JSON_UNESCAPED_UNICODE));
    }

    /**
     * 魔法函数__get.
     *
     * @param string $name 属性名称
     *
     * @return mixed
     */
    public function __get($name = '')
    {
        return $this->$name;
    }

    /**
     * 魔法函数__set.
     *
     * @param string $name  属性名称
     * @param mixed  $value 属性值
     *
     * @return mixed
     */
    public function __set($name = '', $value = '')
    {
        $this->$name = $value;
    }
}
