<?php

namespace Outl1ne\NovaSettings;

use function collect;
use function is_array;
use function is_string;

class NovaSettingsInMemoryStore extends NovaSettingsStore
{
    protected $cache = [];

    public function clearCache($keyNames = null)
    {
        // Clear whole cache
        if (empty($keyNames)) {
            $this->cache = [];
            return;
        }

        // Clear specific keys
        if (is_string($keyNames)) $keyNames = [$keyNames];
        foreach ($keyNames as $key) {
            unset($this->cache[$key]);
        }
    }

    protected function getCached($keyNames = null)
    {
        if (is_string($keyNames)) return $this->cache[$keyNames] ?? null;

        return is_array($keyNames) && !empty($keyNames)
            ? collect($this->cache)->only($keyNames)->toArray()
            : $this->cache;
    }

    protected function setCached($keyName, $value)
    {
        $this->cache[$keyName] = $value;
    }
}
