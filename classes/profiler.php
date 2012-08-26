<?php
namespace Plupload;

class Profiler extends \Profiler
{
    public static function stop_profiling()
    {
        static::$profiler = null;
    }
}