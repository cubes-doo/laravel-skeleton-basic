<?php

/*
|--------------------------------------------------------------------------
| Helper Funstions - Standard
|--------------------------------------------------------------------------
|   Helper functions may be added to this file if they solve a unique problem,
|   and are used multiple times in the application codebase.
|   The problem that they solve must be simple enough, so that it doesn't
|   require assistance from other classes or traits.
|   This file is autoloaded via composer, under the files key.
|
|   Rules:
|       #1 if (!function_exists('method_name')) is a must
|       #2 documentation is a must
|       #3 no repeating functions are allowed, if a function solves a
|           problem the same way & returns a value the same as another function
|           it is considered a duplicate
|
 */

if (! function_exists('array_keys_exist')) {
    /**
     * Checks if all $keys exist in the $arr array
     *
     * @param array $keys
     * @param array $arr
     *
     * @return bool
     *
     * @author Alexander Dickson <alex@alexanderdickson.com>
     * @author Aleksa Cvijić     <aleksa.cvijic@cubes.rs>
     * @access public
     */
    function array_keys_exist(array $keys, array $arr)
    {
        return ! array_diff_key(array_flip($keys), $arr);
    }
}

if (! function_exists('str_cut')) {
    /**
     * @param type  $text
     * @param type  $limit
     * @param mixed $delimiter
     *
     * @return string
     *
     * @author Aleksandar Stevanović <aleksandar.stevanovic@cubes.rs>
     * @author Aleksa Cvijić         <aleksa.cvijic@cubes.rs>
     * @access public
     */
    function str_cut($text, $limit, $delimiter = '...')
    {
        $cutedSeoDescription = mb_substr($text, 0, $limit);
        return (mb_strlen($text) > $limit) ? mb_substr($text, 0, mb_strrpos($cutedSeoDescription, ' ')) . $delimiter : $text;
    }
}

if (! function_exists('suffix_number_format')) {
    /**
     *  @param  int     $n
     *  @param  int     $precision [optional]
     *
     *  @return type
     *  @access public
     *
     *  @author Radley Sustaire <RadGH> <radleygh@gmail.com> <http://radleysustaire.com>
     *
     *  @link   https://gist.github.com/RadGH/84edff0cc81e6326029c#file-short-number-format-php
     */
    function suffix_number_format($n, $precision = 1)
    {
        if ($n < 900) {
            // 0 - 900
            $n_format = number_format($n, $precision);
            $suffix = '';
        } elseif ($n < 900000) {
            // 0.9k-850k
            $n_format = number_format($n / 1000, $precision);
            $suffix = 'K';
        } elseif ($n < 900000000) {
            // 0.9m-850m
            $n_format = number_format($n / 1000000, $precision);
            $suffix = 'M';
        } elseif ($n < 900000000000) {
            // 0.9b-850b
            $n_format = number_format($n / 1000000000, $precision);
            $suffix = 'B';
        } else {
            // 0.9t+
            $n_format = number_format($n / 1000000000000, $precision);
            $suffix = 'T';
        }
        // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
        // Intentionally does not affect partials, eg "1.50" -> "1.50"
        if ($precision > 0) {
            $dotzero = '.' . str_repeat('0', $precision);
            $n_format = str_replace($dotzero, '', $n_format);
        }
        return $n_format . $suffix;
    }
}

if (! function_exists('json_config')) {
    /**
     * Returns an array of configurations from a `.json` config file
     *
     * Prereq.'s:
     *  - the file whose configuration we want must be stored in the config/ folder
     *  - the file whose configuration we want must be of type .json
     *
     * @param string $fileName
     *
     * @return array
     */
    function json_config($fileName)
    {
        $file = base_path('config' . DIRECTORY_SEPARATOR . $fileName . '.json');

        if (! file_exists($file)) {
            return [];
        }

        return json_decode(file_get_contents($file), true) ?? [];
    }
}

if (! function_exists('get_models')) {
    /**
     * Returns an array of model names
     *
     * Models are looked for in app/Models/ and namespaced further with dot syntax.
     *
     * @return array
     */
    function get_models()
    {
        $path = app_path() . '/Models';
        $out = [];
        $results = scandir($path);

        foreach ($results as $result) {
            if ($result === '.' || $result === '..') {
                continue;
            }
            
            $filename = $path . '/' . $result;
            if (is_dir($filename)) {
                if ($result !== 'Utils') {
                    $out = array_merge($out, array_map(function ($v) use ($result) {
                        $result = snake_case(substr($result, 0, -4));
                        return $result . '.' . $v;
                    }, get_models($filename)));
                }
            } else {
                $result = snake_case(substr($result, 0, -4));
                $out[] = $result;
            }
        }

        return $out;
    }
}
