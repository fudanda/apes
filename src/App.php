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

namespace apes;

use apes\Container;
use apes\Exceptions\CoreHttpException;
use Closure;

/**
 * Application
 *
 * 框架应用类
 *
 * 整个框架自身就是一个应用
 *
 * @author TIERGB <https://github.com/TIGERB>
 */
class App
{
    /**
     * 框架加载流程一系列处理类集合
     *
     * @var array
     */
    private $handlesList = [];

    /**
     * 请求对象
     *
     * @var object
     */
    private $request;

    /**
     * 框架实例根目录
     *
     * @var string
     */
    private $rootPath;

    /**
     * 响应对象
     *
     * @var object
     */
    private $responseData;

    /**
     * 运行模式
     * 目前支持fpm/cli/swoole模式
     * 默认为fpm
     * 
     * app running mode 
     * support fpm/cli/swoole
     * default value is fpm
     *
     * @var string
     */
    public $runningMode = 'fpm';


    /**
     * 框架实例
     *
     * @var object
     */
    public static $app;

    /**
     * 是否输出响应结果
     *
     * 默认输出
     *
     * cli模式　访问路径为空　不输出
     *
     * @var boolean
     */
    private $notOutput = false;

    /**
     * 服务容器
     *
     * @var object
     */
    public static $container;

    /**
     * 项目目录
     *
     * @var string
     */
    public $apesPath = '';
    /**
     * 构造函数
     *
     * @param  string $rootPath 框架实例根目录
     * @param  string $loader   注入自加载实例
     */
    public function __construct($rootPath, Closure $loader)
    {
        // 运行模式
        $this->runningMode = getenv('EASY_MODE');
        // 根目录
        $this->apesPath   = dirname(__DIR__) . DIRECTORY_SEPARATOR;
        $this->rootPath    = $rootPath ? rtrim($rootPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR : $this->getDefaultRootPath();
        $this->appPath     = $this->rootPath . 'app' . DIRECTORY_SEPARATOR;
        $this->runtimePath = $this->rootPath . 'runtime' . DIRECTORY_SEPARATOR;

        // 注册自加载
        $loader();
        Load::register($this);

        self::$app = $this;
        self::$container = new Container();
    }
    /**
     * 获取应用根目录
     * @access protected
     * @return string
     */
    protected function getDefaultRootPath(): string
    {
        $path = dirname(dirname(dirname($this->apesPath)));

        return $path . DIRECTORY_SEPARATOR;
    }
    /**
     * 魔法函数__get
     *
     * @param  string $name  属性名称
     * @return mixed
     */
    public function __get($name = '')
    {
        return $this->$name;
    }

    /**
     * 魔法函数__set
     *
     * @param  string $name   属性名称
     * @param  mixed  $value  属性值
     * @return mixed
     */
    public function __set($name = '', $value = '')
    {
        $this->$name = $value;
    }

    /**
     * 注册框架运行过程中一系列处理类
     *
     * @param  object $handle handle类
     * @return void
     */
    public function load(Closure $handle)
    {
        $this->handlesList[] = $handle;
    }

    /**
     * 内部调用get
     *
     * 可构建微单体架构
     *
     * @param  string $uri 要调用的path
     * @param  array $argus 参数
     * @return void
     */
    public function get($uri = '', $argus = array())
    {
        return $this->callSelf('get', $uri, $argus);
    }

    /**
     * 内部调用post
     *
     * 可构建微单体架构
     *
     * @param  string $uri 要调用的path
     * @param  array $argus 参数
     * @return void
     */
    public function post($uri = '', $argus = array())
    {
        return $this->callSelf('post', $uri, $argus);
    }

    /**
     * 内部调用put
     *
     * 可构建微单体架构
     *
     * @param  string $uri 要调用的path
     * @param  array $argus 参数
     * @return void
     */
    public function put($uri = '', $argus = array())
    {
        return $this->callSelf('put', $uri, $argus);
    }

    /**
     * 内部调用delete
     *
     * 可构建微单体架构
     *
     * @param  string $uri 要调用的path
     * @param  array $argus 参数
     * @return void
     */
    public function delete($uri = '', $argus = array())
    {
        return $this->callSelf('delete', $uri, $argus);
    }

    /**
     * 内部调用
     *
     * 可构建微单体架构
     *
     * @param  string $method 模拟的http请求method
     * @param  string $uri 要调用的path
     * @param  array $argus 参数
     * @return json
     */
    public function callSelf($method = '', $uri = '', $argus = array())
    {
        $requestUri = explode('/', $uri);
        if (count($requestUri) !== 3) {
            throw new CoreHttpException(400);
        }
        $request = self::$container->get('request');
        $request->method        = $method;
        $request->requestParams = $argus;
        $request->getParams     = $argus;
        $request->postParams    = $argus;
        $router  = self::$container->get('router');
        $router->moduleName     = $requestUri[0];
        $router->controllerName = $requestUri[1];
        $router->actionName     = $requestUri[2];
        $router->routeStrategy  = 'micromonomer';
        $router->init($this);
        return $this->responseData;
    }

    /**
     * 运行应用
     * 
     * fpm mode
     *
     * @param  Request $request 请求对象
     * @return void
     */
    public function run(Closure $request)
    {
        self::$container->set('request', $request);
        foreach ($this->handlesList as $handle) {
            $handle()->register($this);
        }
    }

    /**
     * 生命周期结束
     *
     * 响应请求
     * @param  Closure $closure 响应类
     * @return json
     */
    public function response(Closure $closure)
    {
        /**
         * 错误处理handle里 fatal error是通过register_shutdown_function注册的函数获取的
         * 防止fatal error时输出两会json 所以response也注册到register_shutdown_function的队列中
         * 
         * TODO 这个地方要重构
         */
        register_shutdown_function([$this, 'responseShutdownFun'], $closure);
    }

    /**
     * shutdown response
     *
     * @param Closure $closure
     * @return void
     */
    public function responseShutdownFun(Closure $closure)
    {
        if ($this->notOutput === true) {
            return;
        }
        if ($this->runningMode === 'cli') {
            $closure($this)->cliModeSuccess($this->responseData);
            return;
        }

        $useRest = self::$container->getSingle('config')
            ->config['rest_response'];

        if ($useRest) {
            $closure($this)->restSuccess($this->responseData);
        }
        $closure($this)->response($this->responseData);
    }

    /**
     * 生命周期结束
     *
     * 响应请求
     * @param  Closure $closure 响应类
     * @return json
     */
    public function responseSwoole(Closure $closure)
    {
        $closure()->header('Content-Type', 'Application/json');
        $closure()->header('Charset', 'utf-8');
        $closure()->end(json_encode($this->responseData));
    }
}
