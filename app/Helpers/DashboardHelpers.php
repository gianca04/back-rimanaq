<?php

if (!function_exists('dashboard_config')) {
    /**
     * Get dashboard configuration value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function dashboard_config($key = null, $default = null)
    {
        if (is_null($key)) {
            return config('dashboard');
        }

        return config("dashboard.{$key}", $default);
    }
}

if (!function_exists('dashboard_option')) {
    /**
     * Get dashboard option
     *
     * @param string $option
     * @param mixed $default
     * @return mixed
     */
    function dashboard_option($option, $default = null)
    {
        return dashboard_config("options.{$option}", $default);
    }
}

if (!function_exists('dashboard_validation')) {
    /**
     * Get validation rules for dashboard
     *
     * @param string $type
     * @param string $rule
     * @param mixed $default
     * @return mixed
     */
    function dashboard_validation($type, $rule = null, $default = null)
    {
        if (is_null($rule)) {
            return dashboard_config("validation.{$type}", []);
        }

        return dashboard_config("validation.{$type}.{$rule}", $default);
    }
}