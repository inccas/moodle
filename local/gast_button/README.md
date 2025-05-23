# moodle-local_gast_button

A plugin to create a button from shortcode to connect to g.a.s.t. services

## Usage

Enter `[gastbutton]Name of the button[/gastbutton]` into any text field. This will display a left-aligned button labeled _Name of the button_. 

Optional attributes
* `align=[center, right]`  
  Set the alignment of the button, if default alignment (=left) does not fit the needs.
  `[gastbutton align=center]Name of the button[/gastbutton]`  
  `[gastbutton align=right]Name of the button[/gastbutton]`  
* productid=&lt;number&gt;  
  Overrides the productId set in config.php  
  `[gastbutton productid=4]Name of the button[/gastbutton]`

A JWT is generated on button click, which includes an obfuscated userId, which is sent to g.a.s.t. to retrieve a redirectUrl. The user is then redirected to the url.

## Configuration (config.php)

    $CFG->gast_button = [
        'clientId' => ...,
        'productId' => 4, // DUO
        'salt' => '...',
        'env' => [
            'test' => [
                'url'=>'https://apprex.gast.de/gast-service-auth/api/public/client/entry',
                'secretkey'=>'...',
            ],
            'prod' => [
                'url'=>'https://api.gast.de/auth/api/public/client/entry',
                'secretkey'=>'...',
            ]
        ],
        'activeenv' => 'prod',
    ];

