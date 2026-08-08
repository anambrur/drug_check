<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class XSS
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $input = $request->all();
        $allowed = '<h1><h2><h3><h4><h5><h6><b><strong><p><span><ol><ul><li><div><a><br><pre><img><table><tbody><tr><td><th><em><i><u><small><strike><center><font><link><meta><hr><form><input><option><blockquote><figure><iframe>';
        array_walk_recursive($input, function (&$value) use ($allowed) {
            // Only strip tags on strings — null/int/bool must stay as-is
            // (strip_tags(null) becomes '' and breaks nullable integer columns).
            if (is_string($value)) {
                $value = strip_tags($value, $allowed);
            }
        });
        $request->merge($input);
        return $next($request);
    }
}
