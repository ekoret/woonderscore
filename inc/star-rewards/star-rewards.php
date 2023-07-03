<?php

add_action('wp_head', 'output_user_star_rewards');
function output_user_star_rewards(){
    $user_id = get_current_user_id();
    $star_rewards = get_user_meta($user_id, 'star_rewards', true);
    echo 'Total star rewards: '.($star_rewards ? $star_rewards * 10 : 0);
}

/**
 * Handle adding star rewards on order completed
 */
add_action('woocommerce_order_status_completed', 'handle_star_reward_order_complete', 10, 2);
function handle_star_reward_order_complete($order_id, $order){
    $user_id = $order->get_user_id();
    $current_date = new DateTime(current_time('Y-m-d'));

    $reward_amount = 1;
    $is_double_stars = false;

    // Check if it's double star day
    if(is_double_stars_day($current_date)){
        $reward_amount = 2;
        $is_double_stars = true;
    }

    // Check items in cart if they're apart of star rewards and tally reward amount

    $new_total_stars = add_star_reward_meta($user_id, $reward_amount);

    $reward_amount_display = $reward_amount * 10;
    $is_double_stars = $is_double_stars ? "YES" : "NO";
    $new_total_stars_display = $new_total_stars * 10;

    $order_message = sprintf("Added %d stars for order completion.\nOrderd on Double Star: %s\nTotal Stars: %d", $reward_amount_display, $is_double_stars, $new_total_stars_display);
    $order->add_order_note($order_message, false);
}

function add_star_reward_meta($user_id, $reward_amount){
    $meta_key = "star_rewards";
    $user_total_star_rewards = get_user_meta($user_id, $meta_key, true);

    $updated_user_total_star_rewards = $reward_amount;

    if ( ! empty($user_total_star_rewards)){
        $updated_user_total_star_rewards = $user_total_star_rewards + $reward_amount;
    }

    update_user_meta($user_id, $meta_key, $updated_user_total_star_rewards);

    return $updated_user_total_star_rewards;
}

/**
 * Handling removing stars on order cancelled
 */
add_action('woocommerce_order_status_cancelled', 'handle_star_reward_order_cancelled', 10, 2);
function handle_star_reward_order_cancelled($order_id, $order){
    $user_id = $order->get_user_id();

    $order_made_date = new DateTime($order->get_date_completed()->date('Y-m-d'));

    $reward_amount = 1;
    $is_double_stars = false;
    
    if( ! empty($order_made_date)){
        
        if( is_double_stars_day($order_made_date) ){
            $reward_amount = 2;
            $is_double_stars = true;
        }
    }

    $new_total_stars = subtract_star_reward_meta($user_id, $reward_amount);

    $reward_amount_display = $reward_amount * 10;
    $is_double_stars = $is_double_stars ? "YES" : "NO";
    $new_total_stars_display = $new_total_stars * 10;

    $order_message = sprintf("Removed %d stars for order cancelled.\nOrdered on Double Star: %s\nTotal Stars: %d", $reward_amount_display, $is_double_stars, $new_total_stars_display);
    $order->add_order_note($order_message, false);
}

function subtract_star_reward_meta($user_id, $sub_reward_amount){
    $meta_key = "star_rewards";
    $user_total_star_rewards = get_user_meta($user_id, $meta_key, true);

    $updated_user_total_star_rewards = $sub_reward_amount;

    if ( ! empty($user_total_star_rewards)){
        $updated_user_total_star_rewards = $user_total_star_rewards - $sub_reward_amount;
    }

    if( $updated_user_total_star_rewards < 0){
        $updated_user_total_star_rewards = 0;
    }

    update_user_meta($user_id, $meta_key, $updated_user_total_star_rewards);

    return $updated_user_total_star_rewards;
}


/**
 * Helpers
 */
function is_double_stars_day($date){
    $day_of_week = $date->format('N'); // Get the day of the week as a number (1 = Monday, 7 = Sunday)

    // Add additional checks for example if there is scheduling

    return $day_of_week == 1 ? true : false;

}