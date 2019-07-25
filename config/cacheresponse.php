<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 19-7-24
 * Time: 下午4:10
 */

return [
    // 请求方法为配置值才缓存，多个以,组合，如'GET,POST',兼容大小写
    'allow_methods' => env('CACHE_RESPONSE_METHODS', 'GET'),

    // response状态码为配置值才缓存，多个以,拼接，如'200,204,304'
    'allow_status_codes' => env('CACHE_RESPONSE_STATUS_CODES', '200'),

    // 缓存结果数据过期时间，单位分钟
    'expire_time' => env('CACHE_RESPONSE_EXPIRE', 10),

    // 不缓存路由
    'except_routes' => [
//        'wechat-serve'
    ],

    // 强制重置缓存提醒参数，如https://location/getData?_reset=1，将会重置与当前路由一致的缓存
    'reset_field' => env('CACHE_RESPONSE_RESET_FIELD', '_reset'),

    // 缓存key前缀
    'cache_key_prefix' => env('CACHE_RESPONSE_KEY_PREFIX', 'cache_response:'),

    // 缓存tag标志，用于命令行'cache-response:clear'清除所有缓存做标记，如果缓存驱动非redis、memcached不可用
    'cache_tag' => env('CACHE_RESPONSE_CACHE_TAG', 'cache_response_tag'),
];