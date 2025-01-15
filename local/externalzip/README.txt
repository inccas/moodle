This package is a "local" plugin which allows Moodle to use external
command-line zip and unzip utilities rather than the PHP ZipArchive library.
It's intended as a workaround to ZipArchive's inability to handle large zip
files. (See MDL-34388)

I have only tested this plugin in Ubuntu 12.04. It probably won't work on
Windows, and it might not work on other platforms if their "zip" and "unzip"
utilities have different command-line options and output.


TO INSTALL (version 0.9.2):

1) Copy the externalzip directory into your ($CFG->dirroot)/local directory

2) Apply the patch file to lib/filestorage/zip_archive.php by cd'ing into 
your $CFG->dirroot directory and running this command:

patch -p1 < local/externalzip/lib-filestorage-zip_archive.php.patch

3) Log in to your Moodle and go to the Notifications page to install the 
DB changes for the update.

4) After installation navigate to "Site Administration" -> "Plugins" -> 
"Local plugins" -> "externalzip" -> "Settings for external zip utility" to 
choose between using ZipArchive and the external zip utility.


TO UPGRADE (from version 0.9 or 0.9.1):

Follow these steps if you have installed version 0.9 or 0.9.1 and wish to
upgrade to version 0.9.2:

1) Delete your ($CFG->dirroot)/local/externalzip directory

2) Copy the 0.9.2 externalzip directory into your ($CFG->dirroot)/local
directory.

3) Log in to your Moodle and go to the Notifications page to update the version
number of your installation.


CHANGELOG:

0.9: Original version of the plugin

0.9.1: Corrected a typo in the installation instructions, incorporated Ruslan
Kabalin's improvement to the admin interface which issues a warning if the zip
and unzip utilities are not found where expected.

0.9.2: Included German Valero's Mexican Spanish translation, added upgrade
instructions to the README.
