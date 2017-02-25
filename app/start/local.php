<?php

    // Gets an array element or returns a default value
	function arr_get(&$var, $default=null) {
        return isset($var) ? $var : $default;
    }