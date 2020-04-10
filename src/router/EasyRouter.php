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

use apes\App;
use apes\Router\Router;
use apes\Exceptions\CoreHttpException;
// use ReflectionClass;
use Closure;

/**
 * 路由入口
 * 
 * the router entrance
 *
 * @author TIERGB <https://github.com/TIGERB>
 */
class EasyRouter implements Router
{
    /**
     * 框架实例
     * 
     * the apes instance
     *
     * @var App
     */
    private $app;

    /**
     * 配置实例
     * 
     * the config instance
     *
     * @var
     */
    private $config;

    /**
     * 请求对象实例
     * 
     * the request instance
     *
     * @var
     */
    private $request;

    /**
     * 默认模块.
     *
     * default module
     * 
     * @var string
     */
    private $moduleName = '';

    /**
     * 默认控制器
     * 
     * default controller
     * 
     * @var string
     */
    private $controllerName = '';

    /**
     * 默认操作.
     * 
     * default action
     *
     * @var string
     */
    private $actionName = '';

    /**
     * 类文件路径.
     * 
     * class path
     *
     * @var string
     */
    private $classPath = '';

    /**
     * 类文件执行类型.
     * 
     * ececute type
     *
     * @var string
     */
    private $executeType = 'controller';

    /**
     * 请求uri.
     * 
     * the request uri
     *
     * @var string
     */
    private $requestUri = '';

    /**
     * 路由策略.
     * 
     * the current router strategy
     *
     * @var string
     */
    private $routeStrategy = '';

    /**
     * 路由策略映射
     * 
     * the router strategy map
     *
     * @var array
     */
    private $routeStrategyMap = [
        'general'      => 'apes\Router\General',
        'pathinfo'     => 'apes\Router\Pathinfo',
        'user-defined' => 'apes\Router\Userdefined',
        'micromonomer' => 'apes\Router\Micromonomer',
        'job'          => 'apes\Router\Job'
    ];

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

    /**
     * 注册路由处理机制.
     *
     * @param App $app 框架实例
     * @param void
     */
    public function init(App $app)
    {
        // 注入当前对象到容器中 register this object to the service container
        $app::$container->set('router', $this);
        // request uri
        $this->request        = $app::$container->get('request');
        $this->requestUri     = $this->request->server('REQUEST_URI');
        // App
        $this->app            = $app;
        // 获取配置 get config
        $this->config         = $app::$container->getSingle('config');
        // 设置默认模块 set default module
        $this->moduleName     = $this->config->config['route']['default_module'];
        // 设置默认控制器 set default controller
        $this->controllerName = $this->config->config['route']['default_controller'];
        // 设置默认操作 set default action
        $this->actionName     = $this->config->config['route']['default_action'];

        // 路由决策 judge the router strategy
        $this->strategyJudge();

        // 路由策略 the current router strategy
        (new $this->routeStrategyMap[$this->routeStrategy])->route($this);

        // to do　等待优化
        $this->makeClassPath($this);

        // 自定义路由判断
        if ((new $this->routeStrategyMap['user-defined'])->route($this)) {
            return;
        }

        // 启动路由
        $this->start();
    }

    /**
     * 路由策略决策
     *
     * @param void
     */
    public function strategyJudge()
    {
        // 路由策略
        if (!empty($this->routeStrategy)) {
            return;
        }

        // 任务路由
        if ($this->app->runningMode === 'cli' && $this->request->get('router_mode') === 'job') {
            $this->routeStrategy = 'job';
            return;
        }

        // 普通路由
        if (strpos($this->requestUri, 'index.php') || $this->app->runningMode === 'cli') {
            $this->routeStrategy = 'general';
            return;
        }
        $this->routeStrategy = 'pathinfo';
    }

    /**
     * get class path
     *
     * @return void
     */
    public function makeClassPath()
    {
        // 任务类
        if ($this->routeStrategy === 'job') {
            return;
        }

        // 获取控制器类
        $controllerName    = ucfirst($this->controllerName);
        $folderName        = ucfirst($this->config->config['application_folder_name']);
        $this->classPath   = "{$folderName}\\{$this->moduleName}\\Controllers\\{$controllerName}";
    }

    /**
     * 路由机制
     *
     * @param void
     */
    public function start()
    {
        // 判断模块存不存在
        if (!in_array(strtolower($this->moduleName), $this->config->config['module'])) {
            throw new CoreHttpException(404, 'Module:' . $this->moduleName);
        }

        // 判断控制器存不存在
        if (!class_exists($this->classPath)) {
            throw new CoreHttpException(404, "{$this->executeType}:{$this->classPath}");
        }

        // 反射解析当前控制器类　
        // 判断是否有当前操作方法
        // 不使用反射
        // $reflaction     = new ReflectionClass($controllerPath);
        // if (!$reflaction->hasMethod($this->actionName)) {
        //     throw new CoreHttpException(404, 'Action:'.$this->actionName);
        // }

        // 实例化当前控制器
        $controller = new $this->classPath();
        if (!is_callable([$controller, $this->actionName])) {
            throw new CoreHttpException(404, 'Action:' . $this->actionName);
        }

        // 调用操作
        $actionName = $this->actionName;

        // 获取返回值
        $this->app->responseData = $controller->$actionName();
    }
}
