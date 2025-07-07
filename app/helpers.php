<?php

if (!function_exists('generateUniqueColors')) {
    function generateUniqueColors($count) {
        $colors = array();
    
        for ($i = 0; $i < $count; $i++) {
            // Generate a random color in hexadecimal format
            $color = '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6);
            
            // Check if the color is already in the array
            while (in_array($color, $colors)) {
                $color = '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6);
            }
            
            // Add the unique color to the array
            $colors[] = $color;
        }
    
        return $colors;
    }
}