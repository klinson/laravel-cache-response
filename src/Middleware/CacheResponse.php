<?php

namespace Klinson\CacheResponse\Middleware;

use Closure;
use Klinson\CacheResponse\Facades\CacheResponse as CacheResponseFacade;

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
        if (CacheResponseFacade::checkRequest($request)) {
            $cache_key = CacheResponseFacade::getCacheKey($request);

            // 没有重置缓存请求
            if (! CacheResponseFacade::hasReset($request)) {
                $response = CacheResponseFacade::getCache($cache_key);
                if ($response) {
                    return $response;
                }
            } else {
                CacheResponseFacade::forgetCache($cache_key);
            }

            // 此次请求结果将要缓存起来
            $response = $next($request);
            if (CacheResponseFacade::checkCode($response)) {
                CacheResponseFacade::cache($cache_key, $response);
            }

            return $response;
        }

        return $next($request);
    }
}
