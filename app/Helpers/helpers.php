<?php

use Illuminate\Support\Str;

if (! function_exists('uuid')) {
    /**
     * Generate a UUID (version 4)
     *
     * @return string
     */
    function uuid()
    {
        return Str::uuid()->toString();
    }
}

if (! function_exists('ip')) {
    /**
     * Get the client IP address.
     *
     * @return string
     */
    function ip()
    {
        return request()->ip();
    }
}

if (! function_exists('file_path')) {
    /**
     * Get the full file path given the folder path and file name.
     *
     * @param string $path
     * @param string $filename
     * @param string $folder The folder inside the path
     * @return string
     */
    function file_path($path, $filename, $folder = null)
    {
        return rtrim($path, '/') . ($folder ? "/{$folder}/" : '/') . $filename;
    }
}

if (! function_exists('prettify')) {
    /**
     * Prettify the given value.
     *
     * Sample result:
     *  name            Name
     *  age             Age
     *  created_at      Created At
     *  totalAmount     Total Amount
     *  birth_date      Birth Date
     *  theirPetName    Their Pet Name
     *  some-value      Some Value
     *
     * @param string $value
     * @return string
     */
    function prettify($value)
    {
        return title_case(snake_case(camel_case($value), ' '));
    }
}


if (! function_exists('include_route_files')) {
    /**
     * Loops through a folder and requires all PHP files
     * Searches sub-directories as well.
     *
     * @param string $folder
     */
    function include_route_files(string $folder)
    {
        $path = base_path('routes' . DIRECTORY_SEPARATOR . $folder);
        $rdi = new recursiveDirectoryIterator($path);
        $it = new recursiveIteratorIterator($rdi);

        while ($it->valid()) {
            if (! $it->isDot() && $it->isFile() && $it->isReadable() && $it->current()->getExtension() === 'php') {
                require $it->key();
            }

            $it->next();
        }
    }
}

if (! function_exists('log_exception')) {
    /**
     * Log the exception.
     *
     * @param Throwable $exception
     */
    function log_exception(Throwable $exception)
    {
        if (app()->environment('local')) {
            Log::error($exception);
        }

        if (app()->environment('staging') || app()->environment('production')) {
            Log::error($exception);
        }
    }
}
