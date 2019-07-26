<?php

if (!function_exists('cache_response')) {

    /**
     * 设置/获取 CacheResponse
     *
     * @param string|\Illuminate\Http\Request $param1 缓存key或者请求request
     * @param \Illuminate\Http\Response $param2 无此参数则是获取缓存
     *
     * @return \Illuminate\Support\Facades\Cache|\Illuminate\Http\Response|void
     *
     * @throws \Exception
     */
    function cache_response()
    {
        $arguments = func_get_args();

        $cacheResponse = app('cache.response');
        if (empty($arguments)) {
            return $cacheResponse;
        }

        // 获取
        if (! isset($arguments[1])) {
            if (is_string($arguments[0])) {
                return $cacheResponse->getCache($arguments[0]);
            }

            if ($arguments[0] instanceof \Illuminate\Http\Request) {
                if (! $cacheResponse->checkRequest($arguments[0])) {
                    return null;
                }
                return $cacheResponse->getCache($cacheResponse->getCacheKey($arguments[0]));
            }
        }

        // 缓存
        if ($arguments[0] instanceof \Illuminate\Http\Request) {
            $key = $cacheResponse->getCacheKey($arguments[0]);
        } else if (! is_string($arguments[0])) {
            throw new Exception(
                'key内容必须是\Illuminate\Http\Request对象或者缓存key'
            );
        } else {
            $key = $arguments[0];
        }

        if (! $arguments[1] instanceof \Illuminate\Http\Response) {
            throw new Exception(
                '缓存内容必须是\Illuminate\Http\Response对象'
            );
        }

        if (! $cacheResponse->checkCode($arguments[1])) {
            throw new Exception(
                '当前状态码无需缓存'
            );
        }
        return $cacheResponse->cache($key, $arguments[1]);
    }
}

