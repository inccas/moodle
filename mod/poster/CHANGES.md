### 7.0.0 ###

* New maintainer: bdecent gmbh.
* Behat and PHPUnit tests updated and tested on Moodle 4.1 - 4.3.
* Fixed minor coding style issues.
* Added monologo for Moodle 4.x.
* Fixed improper use of require_login method instead of require_course_login (resolves #10).
* New feature: Display block content on course page (based on work by Harald, David und Nicklas @devcamp19 � Thanks!).
 
### 6.0.1 ###

* Behat and PHPUnit tests updated and tested on Moodle 3.9 - 3.11.
* Fixed minor coding style issues reported by phpcs.
* Git `master` branch renamed to `main`.

### 6.0.0 ###

* Improved styling of the "Add a block" widget to distinguish it from other existing
  blocks on the page.
* Dropped support for legacy  Bootstrap 2.x grid and CSS classes. Supported are
  Bootstrap 4.x based themes such as Boost or Classic.
* Fixed minor coding style issues.
* Travis-CI replaced with Github actions.
* Supported Moodle versions 3.9 LTS and higher.

### 5.2.2 ###

* Behat tests updated and tested on Moodle 3.5 - 3.9

### 5.2.1 ###

* Behat tests updated to run on Moodle 3.6

### 5.2.0 ###

* [Privacy API](https://docs.moodle.org/dev/Privacy_API) implemented. The Poster
  plugin does not store any personal data.
* Moodle 3.5 added as a supported version.

### 5.1.0 ###

* In editing mode, the "Add a block" drop down selector is now displayed
  in the Boost based themes, too - making it easier and more intuitive
  to add blocks to the poster regions.

### 5.0.0 ###

* Improved the styling of the poster editing page
* Tested with Moodle 3.2 and 3.3 under Boost, Clean and More themes

### 4.0.1 ###

* Added travis-ci support
* Coding style fixes

### v4 ###

* Added behat tests for the main features (adding instance, adding contents)
* Checked compatibility with Moodle 3.0 version
* Switching over to the stable maturity level

### v3 ###

* Fixed the required Moodle 2.7 version, credit goes to Howard Miller for the report.

### v2 ###

* Fixed the missing module help string (displayed when adding the poster into the course).

### v1 ###

* Added support for backup & restore.
* Fixed usage of deprecated `add_intro_editor()` method in Moodle 2.9 and higher.
* The blocks docking widget is now hidden by CSS.
* Reaching the beta maturity as all planned features are implemented now. Staring to support proper upgrade procedures from now.

### v0 ###

* Initial release of the module, still in alpha version.
* Basic functionality works.
* Needs to add backup & restore support yet.
