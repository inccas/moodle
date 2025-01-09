<?php
$callbacks = [
    [
        'hook' => \core\hook\output\before_standard_head_html_generation::class,
        'callback' => [\theme_boost_union_phd\local\hook_callbacks::class, 'before_standard_head_html'],
    ],
];
