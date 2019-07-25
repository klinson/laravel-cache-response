<?php

namespace Klinson\CacheResponse\Middleware;

use Closure;
use Cache;

class CacheResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 验证当前请求是否需求缓存
        if ($this->check($request)) {
            $params = $request->query();
            $cache_key = $this->getCacheKey($params);
            $reset_field = config('cacheresponse.reset_field', '_reset');

            // 没有重置缓存请求
            if (! isset($params[$reset_field]) || !$params[$reset_field]) {
                if (
                    ($data = Cache::tags(config('cacheresponse.cache_tag', 'cache_response_tag'))->get($cache_key, false)) !== false
                ) {
                    $data = json_decode($data, true);
                    if ($data) {
                        return $this->createResponse($data);
                    }
                }
            } else {
                Cache::tags(config('cacheresponse.cache_tag', 'cache_response_tag'))->forget($cache_key);
            }

            // 此次请求结果将要缓存起来
            $response = $next($request);
            if ($this->checkCode($response)) {
                $this->cache($cache_key, $response);
            }

            return $response;
        }

        return $next($request);
    }

    /**
     * 验证当前请求是否是缓存路由
     * @param \Illuminate\Http\Request $request
     * @author klinson <klinson@163.com>
     * @return bool
     */
    protected function check($request)
    {
        $methods = config('cacheresponse.allow_methods', 'get');
        $methods = explode(',', strtoupper($methods));
        if (! in_array($request->method(), $methods)) {
            return false;
        }
        if ($except_routes = config('cacheresponse.except_routes', [])) {

            foreach ($except_routes as $except) {
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
     * 获取当前请求缓存key
     * @param array $params
     * @author klinson <klinson@163.com>
     * @return string
     */
    protected function getCacheKey($params)
    {
        $cache_prefix = config('cacheresponse.cache_key_prefix', 'cache_response:');
        // 移除重置字段
        unset($params[config('cacheresponse.reset_field', '_reset')]);
        ksort($params);
        $key = md5(http_build_query($params));

        return $cache_prefix.$key;
    }

    /**
     * 验证结果状态码是否符合缓存条件
     * @param $response
     * @author klinson <klinson@163.com>
     * @return bool
     */
    protected function checkCode($response)
    {
        $codes = config('cacheresponse.allow_status_codes', '200');
        $codes = explode(',', $codes);
        if (! in_array($response->getStatusCode(), $codes)) {
            return false;
        }
        return true;
    }

    /**
     * 缓存结果
     * @param $cache_key
     * @param $response
     * @author klinson <klinson@163.com>
     */
    protected function cache($cache_key, $response)
    {
        Cache::tags(config('cacheresponse.cache_tag', 'cache_response_tag'))->put(
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
     * 根据缓存内容进行生成返回值
     * @param $data
     * @author klinson <klinson@163.com>
     * @return $this
     */
    protected function createResponse($data)
    {
        return response($data['content'], $data['status_code'])
            ->header('Content-Type', $data['content-type'])
            ->setProtocolVersion($data['version']);
    }
}
