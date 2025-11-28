<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IncreaseInputLimits
{
    public function handle(Request $request, Closure $next)
    {
        // នេះជាវិធីតែមួយគត់ដែល Laravel 12 អនុញ្ញាត!
        ini_set('max_input_vars', 10000);
        ini_set('post_max_size', '80M');
        ini_set('upload_max_filesize', '50M');
        ini_set('max_file_uploads', 200);
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');

        return $next($request);
    }
}