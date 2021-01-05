<?php

namespace App\Http\Middleware;

use Closure;

class LoadMathJax
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $mathjax = '<script type="text/javascript" id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>';
        $request->merge(['mathjax' => $mathjax]);
        return $next($request);
    }
}
