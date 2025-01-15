<?php

/**
 * Extending admin_setting_configselect for executable files validation
 */
class externalzip_setting_ziphandler extends admin_setting_configselect {
    /**
     * Save a setting
     *
     * @param string $data
     * @return string empty of error string
     */
    public function write_setting($data) {
        // If user wants to use external zip command, make sure the file exists and executable.
        if ($data === 'externalzip') {
            $validated = $this->validate($data);
            if ($validated !== true) {
                return $validated;
            }
        }
        return parent::write_setting($data);
    }
    /**
     * Validate that we have access to zip/unzip
     *
     * @param string data
     * @return mixed true if ok string if error found
     */
    public function validate($data) {
        $commands = array('zip', 'unzip');
        $error = '';
        foreach ($commands as $command) {
            $executable = get_config('local_externalzip', $command);
            if (!file_exists($executable) || !is_executable($executable)) {
                $error .= get_string('pathto' . $command . 'error', 'local_externalzip') . "\n";
            }
        }

        if (empty($error)) {
            return true;
        } else {
            return $error;
        }
    }
}
