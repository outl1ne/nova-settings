<?php

namespace Outl1ne\NovaSettings;

use Illuminate\Support\Facades\Cache;

use function collect;
use function is_array;
use function is_string;

class NovaSettingsCacheStore extends NovaSettingsStore
{
    /** @var \Illuminate\Contracts\Cache\Repository */
    private $cache;

    public function __construct()
    {
        $this->cache = Cache::store(config('nova-settings.cache.store'));
    }

    public function clearCache($keyNames = null)
    {
        // Clear whole cache
        if (empty($keyNames)) {
            $this->getSettingsModelClass()::all(['key'])->each(function ($setting) {
                $this->cache->forget($this->getCacheKey($setting->key));
            });

            return;
        }

        // Clear specific keys
        if (is_string($keyNames)) $keyNames = [$keyNames];
        foreach ($keyNames as $key) {
            $this->cache->forget($this->getCacheKey($key));
        }
    }

    protected function getCached($keyNames = null)
    {
        if (is_string($keyNames)) {
            return $this->cache->get($this->getCacheKey($keyNames));
        }

        if (is_array($keyNames)) {
            return collect($keyNames)
                ->mapWithKeys(function ($key) {
                    if (!$this->cache->has($this->getCacheKey($key))) return [];

                    return [$key => $this->getCached($key)];
                })
                ->toArray();
        }

        return [];
    }

    protected function setCached($keyName, $value)
    {
        $this->cache->forever($this->getCacheKey($keyName), $value);
    }

    private function getCacheKey($key)
    {
        return config('nova-settings.cache.prefix', 'nova-settings:') . $key;
    }
}
