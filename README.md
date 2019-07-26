# klinson/laravel-cache-response

[![Packagist License](https://poser.pugx.org/barryvdh/laravel-debugbar/license.png)](http://choosealicense.com/licenses/mit/)

## 描述/Description

Laravel请求结果自动缓存中间件，缓存返回数据，适用于接口返回json或其他格式数据

> 依赖缓存，缓存驱动必须是`redis`、`memcached`

## 安装/Installation


### 安装包/Install Package

```shell
composer require klinson/laravel-cache-response
```

### 配置/Configuration

- Laravel >= 5.5+, laravel支持 [Package Discovery](https://laravel.com/docs/5.5/packages#package-discovery), 可直接使用
- laravel < 5.5, 需要在`config/app.php`中`providers`数组中加入下面一条

    ```php
    Klinson\CacheResponse\CacheResponseServiceProvider::class,
    ```
    
    需要在`config/app.php`中`aliases`数组中加入下面一条
    ```php
    'CacheResponse' => Klinson\CacheResponse\CacheResponse::class,
    ```

### 发布配置/Publish Configuration

```shell
php artisan vendor:publish --provider="Klinson\CacheResponse\CacheResponseServiceProvider"
```

## 使用/Usage

可以在路由中指定使用`cache_response`中间件，也可以在全局配置中进行加入中间件

```php
Route::get('data', 'DataController@all')->middleware('cache_response');
```

助手函数`cache_response()`

```php
// 获取CacheResponse对象
cache_response()

// 获取$cache_key下的缓存返回Response对象
cache_response($cache_key)

// 获取$request下的缓存返回Response对象
cache_response($request)

// 设置缓存
cache_response($request, $response)
```

## 清除所有缓存/Clear All Cache
> 仅在缓存驱动是`redis`、`memcached`有效
```shell
php artisan cache-response:clear
```