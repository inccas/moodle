# moodle-local_lernlandkarte

A plugin to create a simple lightbox with modal overlay. 
Allows zooming and panning of the visible part of the image if zoom > 100

## Usage

* Requires config.php update
  ```
  $CFG->lernlandkarte = [
    'imagesrc' => '/static/Campus/lernlandkarte-alles-1440px-72dpi.png',
    'x' => 50,
    'y' => 50,
    'zoom' => 100,
  ];
  ```
* Requires filter Shortcodes to be active on pages where `[lernlandkarte][/lernlandkarte]` is used

Enter `[lernlandkarte][/lernlandkarte]` into any text field. This will display the lernlandkarte image. 
Default values specified in config.php are used.

Attributes/parameters
* `[lernlandkarte]/path/to/my/lernlandkarte.png[/lernlandkarte]`
  Uses /path/to/my/lernlandkarte.png and overrides default image.
* `x=[0...100] y=[0...100]`   
  Overrides the default x and y positions of lernlandkarte image in viewbox
  `[lernlandkarte x=20.5 y=80.2]/path/to/lernlandkarte.png[/lernlandkarte]`
  Unit: %, moves the point at x=20%, y=80% to the center of the viewbox.
* `zoom=[100...2000]`
  Unit: %, 100% = full image display, > 100% zoomed 
  Overrides the initial zoom value (=100%).
  `[lernlandkarte zoom=500][/lernlandkarte]`




