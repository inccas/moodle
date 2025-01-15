<?php

if ($hassiteconfig) {
    global $CFG;
    require_once($CFG->dirroot.'/local/externalzip/lib.php');
    require_once($CFG->dirroot.'/local/externalzip/adminlib.php');

    $ADMIN->add(
            'localplugins',
            new admin_category(
                    'externalzipcat',
                    'externalzip'
            )
    );
    $page = new admin_settingpage(
            'externalzip',
            'Settings for external zip utility'
    );
    $page->add(
            new externalzip_setting_ziphandler(
                    'local_externalzip/ziphandler',
                    get_string('ziphandler', 'local_externalzip'),
                    get_string('ziphandler_help', 'local_externalzip'),
                    'phpziparchive',
                    array(
                            LOCAL_EXTERNALZIP_PHP => get_string('phpziparchive_option', 'local_externalzip'),
                            LOCAL_EXTERNALZIP_EXT => get_string('externalzip_option', 'local_externalzip'),
                    )
            )
    );
    $page->add(
            new admin_setting_configexecutable(
                    'local_externalzip/zip',
                    get_string('pathtozip', 'local_externalzip'),
                    get_string('pathtozip_help', 'local_externalzip'),
                    '/usr/bin/zip'
            )
    );
    $page->add(
            new admin_setting_configexecutable(
                    'local_externalzip/unzip',
                    get_string('pathtounzip', 'local_externalzip'),
                    get_string('pathtounzip_help', 'local_externalzip'),
                    '/usr/bin/unzip'
            )
    );
    $ADMIN->add('externalzipcat', $page);
}