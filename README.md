# Klinson/CacheResponse

[![Packagist License](https://poser.pugx.org/barryvdh/laravel-debugbar/license.png)](http://choosealicense.com/licenses/mit/)

## Description/描述

laravel 请求结果自动缓存

## Installation/安装


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
    
- Lumen(未实测)在`bootstrap/app.php`中进行注册，加入如下代码
    ```php
    $app->register(Klinson\CacheResponse\CacheResponseServiceProvider::class);
    }
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

## 清除所有缓存
> 仅在缓存驱动是`redis`、`memcached`有效
```shell
php artisan cache-response:clear
```