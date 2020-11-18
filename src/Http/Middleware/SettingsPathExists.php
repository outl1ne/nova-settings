<?php

namespace OptimistDigital\NovaSettings\Http\Middleware;


use OptimistDigital\NovaSettings\NovaSettings;

class SettingsPathExists
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        $path = $request->has('path') ? $request->get('path') : 'general';

        return NovaSettings::doesPathExist($path) ? $next($request) : abort(404);
    }
}
