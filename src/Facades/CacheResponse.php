<?php

namespace Klinson\CacheResponse\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Admin.
 *
 * @method static void forgetCache($cache_key)
 * @method static string getCacheKey($params)
 * @method static bool checkRequest(\Illuminate\Http\Request $request)
 * @method static bool hasReset(\Illuminate\Http\Request $request)
 * @method static bool|\Illuminate\Http\Response getCache($cache_key)
 * @method static bool checkCode(\Illuminate\Http\Response $response)
 * @method static void cache($cache_key, \Illuminate\Http\Response $response)
 *
 * @see \Klinson\CacheResponse\CacheResponse
 */
class CacheResponse extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'CacheResponse';
    }
}
