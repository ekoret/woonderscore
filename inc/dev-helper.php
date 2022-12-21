<?php

function display_template(){
        global $template;
        echo basename($template);
}
// add_action('wp_head', 'display_template');