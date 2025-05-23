# moodle-theme_vorsprung

The dedicated theme for project VORSprung.

## Settings

* MathJax
    * Add `mhchem.js` to `extensions: ["AMSmath.js", "AMSsymbols.js", "mhchem.js"] ,`
* H5P-Einstellungen
    * Install MathDisplay for mod_hvp
        * mod_hvp | enable_save_content_state: Yes
        * mod_hvp | send_usage_statistics: No
        * Download h5p-math-display-1.0.7.h5p from https://h5p.org/mathematical-expressions
        * H5P Libraries (/mod/hvp/library_list.php)
        * Upload Libraries -> `Choose h5p-math-display-1.0.7.h5p` + Only update existing libraries: Yes -> Upload
        * Add custom config `$CFG->mod_hvp_library_config = ...` to `config.php`
    * Install MathDisplay for core_h5p
        * Manage H5P content types (/h5p/libraries.php#libraries)
        * Upload H5P content types -> h5p-math-display-1.0.7.h5p -> Upload ...
        * Add custom config `$CFG->core_h5p_library_config = ...` to `config.php`