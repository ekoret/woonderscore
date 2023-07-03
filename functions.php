<?php

require get_stylesheet_directory() . '/inc/star-rewards/star-rewards.php';

add_action('wp', 'remove_default_theme_hooks');
function remove_default_theme_hooks(){
    remove_action( 'storefront_homepage', 'storefront_homepage_header', 10);
}

add_action('storefront_homepage', 'storefront_custom_homepage_header', 10);
function storefront_custom_homepage_header() {
    echo "<section id='homepage-slider'>";
    echo "<img src='https://place-hold.it/1920x400/000000/ffffff&fontsize=32' />";
    echo "<section/>";
}


