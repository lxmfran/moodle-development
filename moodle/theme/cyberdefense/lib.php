<?php
// This file is part of Moodle - http://moodle.org/

defined('MOODLE_INTERNAL') || die();

/**
 * Inyecta variables de color ANTES de que Boost compile su SCSS
 */
function theme_cyberdefense_get_pre_scss($theme) {
    $brandcolor = !empty($theme->settings->brandcolor) ? $theme->settings->brandcolor : '#0066cc';
    
    // Sobrescribimos el color primario de Bootstrap/Boost
    return '$primary: ' . $brandcolor . '; $link-color: ' . $brandcolor . ';';
}

/**
 * Inyecta CSS personalizado DESPUÉS de que Boost compile
 */
function theme_cyberdefense_get_extra_scss($theme) {
    $customcss = !empty($theme->settings->customcss) ? $theme->settings->customcss : '';
    
    return $customcss;
}
