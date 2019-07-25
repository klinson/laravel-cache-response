<?php

namespace Klinson\CacheResponse\Console\Command;

use Illuminate\Console\Command;
use Cache;

class Clear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'cache-response:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'clear all cache response，清除cache-response所有缓存';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // 缓存标签目前不支持 file 或 database 缓存驱动，此外，当使用多标签的缓存被设置为永久存储时，使用 memcached 驱动的缓存有着最佳性能表现，因为 Memcached 会自动清除陈旧记录。
        // from https://laravelacademy.org/post/19506.html
        if (Cache::getDefaultDriver() !== 'redis' && Cache::getDefaultDriver() !== 'memcached') {
            $this->error('非redis或memcached驱动不可用');
            return;
        }
        Cache::tags(config('cacheresponse.cache_tag', 'cache_response_tag'))->flush();
        $this->info('清除缓存成功');
    }
}
