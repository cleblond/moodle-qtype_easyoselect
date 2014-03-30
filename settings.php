<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('qtype_easyoselect_options', get_string('easyoselect_options', 'qtype_easyoselect'),
                   get_string('configeasyoselectoptions', 'qtype_easyoselect'), '', PARAM_TEXT));
}

