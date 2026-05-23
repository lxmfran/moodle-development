<?php
defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    // AQUÍ ESTÁ LA MAGIA: El nombre exacto que Moodle busca en la URL
    $settings = new admin_settingpage('themesettingcyberdefense', get_string('configtitle', 'theme_cyberdefense'));

    // 1. Selector de Color
    $name = 'theme_cyberdefense/brandcolor';
    $title = get_string('brandcolor', 'theme_cyberdefense');
    $description = get_string('brandcolor_desc', 'theme_cyberdefense');
    $default = '#39FF14'; 
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $settings->add($setting);

    // 2. Área de CSS Personalizado
    $name = 'theme_cyberdefense/customcss';
    $title = get_string('customcss', 'theme_cyberdefense');
    $description = get_string('customcss_desc', 'theme_cyberdefense');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $settings->add($setting);

    // Añadimos al árbol de Moodle
    $ADMIN->add('themes', $settings);
}
