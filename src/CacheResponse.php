<?php

namespace Klinson\CacheResponse;

use Cache;

class CacheResponse
{
    protected $enable = true;
    protected $local_disable = true;
    protected $tag = 'cache_response_tag';
    protected $prefix = 'cache_response:';
    protected $reset_field = '_reset';
    protected $methods = ['GET'];
    protected $excepts = [];
    protected $codes = [];

    public function __construct()
    {
        $this->enable = config('cacheresponse.enable', true);
        $this->local_disable = config('cacheresponse.local_disable', true);
        $this->tag = config('cacheresponse.cache_tag', 'cache_response_tag');
        $this->prefix = config('cacheresponse.cache_key_prefix', 'cache_response:');
        $this->methods = explode(',', strtoupper(config('cacheresponse.allow_methods', 'GET')));
        $this->excepts = config('cacheresponse.except_routes', []);
        $this->reset_field = config('cacheresponse.reset_field', '_reset');
        $this->codes = explode(',', config('cacheresponse.allow_status_codes', '200'));
    }

    /**
     * 验证当前请求是否是缓存路由
     * @param \Illuminate\Http\Request $request
     * @author klinson <klinson@163.com>
     * @return bool
     */
    public function checkRequest($request)
    {
        // 验证缓存驱动必须是redis或memcached
        if (Cache::getDefaultDriver() !== 'redis' && Cache::getDefaultDriver() !== 'memcached') {
            return false;
        }

        // 全局开关
        if (! $this->enable) {
            return false;
        }

        // 本地环境默认关闭
        if (app()->isLocal() && $this->local_disable) {
            return false;
        }

        // 验证请求方法是否范围内

        if (! in_array($request->method(), $this->methods)) {
            return false;
        }

        // 验证是否是忽略路由
        if ($this->excepts) {
            foreach ($this->excepts as $except) {
                if ($except !== '/') {
                    $except = trim($except, '/');
                }

                if ($request->fullUrlIs($except) || $request->is($except)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 验证当前请求是否是需要重置缓存
     * @param \Illuminate\Http\Request $request
     * @author klinson <klinson@163.com>
     * @return bool
     */
    public function hasReset($request)
    {
        // 没有重置缓存请求
        if ($request->get($this->reset_field)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 缓存结果
     * @param $cache_key
     * @param \Illuminate\Http\Response $response
     * @author klinson <klinson@163.com>
     */
    public function cache($cache_key, $response)
    {
        Cache::tags($this->tag)->put(
            $cache_key,
            json_encode([
                'content' => $response->getContent(),
                'status_code' => $response->getStatusCode(),
                'version' => $response->getProtocolVersion(),
                'content-type' => $response->headers->get('content-type')
            ]),
            config('cacheresponse.expire_time', 10)
        );
    }

    /**
     * 从缓存中获取返回内容
     * @param $cache_key
     * @author klinson <klinson@163.com>
     * @return bool|\Illuminate\Http\Response $response
     */
    public function getCache($cache_key)
    {
        if (($data = Cache::tags(config('cacheresponse.cache_tag', 'cache_response_tag'))->get($cache_key, false)) !== false) {
            $data = json_decode($data, true);
            if ($data) {
                return $this->createResponse($data);
            }
        }
        return false;
    }

    /**
     * 获取当前请求缓存key
     * @param \Illuminate\Http\Request|array $request
     * @author klinson <klinson@163.com>
     * @return string
     */
    public function getCacheKey($request)
    {
        if ($request instanceof \Illuminate\Http\Request) {
            $params = $request->all();

            $pathInfoKey = '_pathinfo';
            while (isset($params[$pathInfoKey])) {
                $pathInfoKey = '_'.$pathInfoKey;
            }
            $params[$pathInfoKey] = $request->getPathInfo();
        } else {
            $params = $request;
        }
        // 移除重置字段
        unset($params[config('cacheresponse.reset_field', '_reset')]);
        ksort($params);
        $key = md5(http_build_query($params));

        return $this->prefix.$key;
    }

    /**
     * 清除指定key缓存
     * @param $cache_key
     * @author klinson <klinson@163.com>
     */
    public function forgetCache($cache_key)
    {
        Cache::tags($this->tag)->forget($cache_key);
    }

    /**
     * 清除缓存
     * @author klinson <klinson@163.com>
     */
    public function clearCache()
    {
        Cache::tags($this->tag)->flush();
    }

    /**
     * 根据缓存内容进行生成返回值
     * @param $data
     * @author klinson <klinson@163.com>
     * @return \Illuminate\Http\Response $response
     */
    public function createResponse($data)
    {
        return response($data['content'], $data['status_code'])
            ->header('Content-Type', $data['content-type'])
            ->setProtocolVersion($data['version']);
    }

    /**
     * 验证结果状态码是否符合缓存条件
     * @param \Illuminate\Http\Response $response
     * @author klinson <klinson@163.com>
     * @return bool
     */
    public function checkCode($response)
    {
        if (! in_array($response->getStatusCode(), $this->codes)) {
            return false;
        }
        return true;
    }
}