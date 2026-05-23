<?php
// This file is part of Moodle - http://moodle.org/

defined('MOODLE_INTERNAL') || die();

$THEME->name = 'cyberdefense';
$THEME->parents = ['boost']; // Hereda toda la estructura visual de Boost
$THEME->enable_dock = false;

// ¡LA LÍNEA MÁGICA QUE ME COMÍ!
$THEME->rendererfactory = 'theme_overridden_renderer_factory';

// Callbacks para inyectar CSS sin romper el diseño base
$THEME->prescsscallback = 'theme_cyberdefense_get_pre_scss';
$THEME->extrascsscallback = 'theme_cyberdefense_get_extra_scss';
