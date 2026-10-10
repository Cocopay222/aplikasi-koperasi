<?php

if (!function_exists('formatNomorWa')) {
    function formatNomorWa($nomor)
    {
        if (empty($nomor)) {
            return '';
        }

        // Remove non-numeric characters
        $nomor = preg_replace('/[^0-9]/', '', $nomor);
        
        // If it starts with 0, replace with 62
        if (substr($nomor, 0, 1) === '0') {
            $nomor = '62' . substr($nomor, 1);
        }
        
        return $nomor;
    }
}
