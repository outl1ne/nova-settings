<?php

namespace Outl1ne\NovaSettings;

use function is_string;

class NovaSettingsNoCacheStore extends NovaSettingsStore
{
    public function clearCache($keyNames = null)
    {
    }

    protected function getCached($keyNames = null)
    {
        if (is_string($keyNames)) return null;

        return [];
    }

    protected function setCached($keyName, $value)
    {
    }
}
