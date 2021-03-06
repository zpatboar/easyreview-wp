<?php

/* 
Plugin Name: EasyReview WordPress Plugin
Plugin URI: http://www.zpb.me
Description: Provides a form to submit a review (Demo plugin)
Author: Boarman Service Company, LLC
Version: 1.0 Alpha
Author URI: http://www.boarmanservice.com
*/

/**
 * Adds a shortcode to be used on a page
 */
function easyreview_form( $atts ){
    //Will be nice when WordPress stops adding slashes regardless of php.ini
    $iPOST = stripslashes_deep($_POST);
    $formvalidated = easyreview_form_validated($iPOST);
    
    if ($formvalidated !== true){
        ob_start();
        require_once(__DIR__.'/tmpl/form.php');
        return ob_get_clean();
    }
    
    return get_option("easyreview_thank_you");

}

add_shortcode( 'easyreview_form', 'easyreview_form' );

function easyreview_form_validated($iPOST){
    $errors = "";
    if (!isset($iPOST['q4oij32r'])){
        return false;
    }
    
    $name = sanitize_text_field($iPOST['your_name']);
    if ($name == ""){
        $errors .= "Please enter a Name<br />";
    }
    
    $email = sanitize_email($iPOST['your_email']);
    if ($email == ""){
        $errors .= "Please enter a valid Email<br />";
    }
    
    $phone = sanitize_text_field($iPOST['your_phone']);
    if ($phone == ""){
        $errors .= "Please enter a Phone Number<br />";
    }
    
    $stars = (int) $iPOST['stars'];
    if ($stars == ""){
        $errors .= "Please enter how Satisfied you are<br />";
    }
    
    $title = sanitize_text_field($iPOST['title']);
    if ($title == ""){
        $title = "A Review from ".$name;
    }
    
    $review = wp_kses_post($iPOST['review']);
    if ($review == ""){
        $errors .= "Please enter your Review<br />";
    }
    
    if ($iPOST['q4oij32r'] != "4" && strtolower($iPOST['q4oij32r']) != "four"){
        $errors .= "Please enter what two plus two is<br />";
    }
    
    if ($errors != ""){
        return $errors;
    }
    
    $review_post = array(
        'post_title' => $title,
        'post_content' => $review,
        'post_status' => 'pending',
        'post_author' => 0,
        'post_category' => array(get_option('easyreview_category')),
        'meta_input' => array(
            'reviewer_name' => $name,
            'reviewer_email' => $email,
            'reviewer_phone' => $phone,
            'stars' => $stars
            )
    );
    
    $post_id = (int) wp_insert_post($review_post);
    
    if ($post_id != 0 && $post_id >= 1){
        $iPOST['apikey'] = get_option('easyreview_apikey');
        $iPOST['site_ref_id'] = $post_id;
        $iPOST['title'] = $title;
        
        $response = wp_remote_post( get_option('easyreview_server_url') , 
            array(
                'method' => 'POST',
                'body' => $iPOST
            )
        );
        
        return true;
    }
    
}

//Admin Submenu
add_action('admin_menu', 'easyreview_plugin_menu');

function easyreview_plugin_menu(){
	add_plugins_page('EasyReview', 'EasyReview', 'activate_plugins', 'easyreview', 'easyreview_plugin_admin');
}

function easyreview_plugin_admin(){
    if (isset($_POST['apikey'])){
        update_option("easyreview_apikey", stripslashes(sanitize_text_field($_POST['apikey'])));
        update_option("easyreview_server_url", stripslashes(sanitize_text_field($_POST['server_url'])));
        update_option("easyreview_thank_you", stripslashes(sanitize_textarea_field($_POST['thank_you'])));
        update_option("easyreview_category", (int) $_POST['review_cat']);
        ?>
        <div id="setting-error-settings_updated" class="updated settings-error"> 
            <p><strong>Configuration saved.</strong></p>
        </div>
        <?php
    }
    ?>
    <div class="wrap">
        <h2>EasyReview Configuration</h2>
        <form method="POST">
            <label for="apikey">Review Category</label><br />
            <select name="review_cat">
                <option> - Select Review Category - </option>
            <?php $categories = get_categories(array('orderby'=>'name', 'order' => 'ASC', 'hide_empty' => false)); 
            foreach ($categories as $cat){
                echo '<option value="'.$cat->cat_ID.'" '.($cat->cat_ID == get_option('easyreview_category') ? "selected" : "").'>'.$cat->cat_name.'</option>';
            }
            ?>
            </select><br />
            <label for="apikey">EasyReview API Key</label><br />
            <input name="apikey" value="<?php echo get_option('easyreview_apikey');?>"><br />
            <label for="apikey">EasyReview Server URL</label><br />
            <input name="server_url" value="<?php echo get_option('easyreview_server_url');?>"><br />
            <label for="thank_you">Thank You Page Content</label><br />
            <?php wp_editor(get_option("easyreview_thank_you"),"thank_you"); ?>
            <p>
                <?php submit_button("Save"); ?>
            </p>
        </form>
    </div>
    <?php
}


add_action('wp_ajax_easyreview_api', 'easyreview_plugin_api');
add_action('wp_ajax_nopriv_easyreview_api', 'easyreview_plugin_api');

function easyreview_plugin_api(){
    $return = array();
    
    switch ($_POST['apiaction']){
        case 'publish':
            wp_publish_post( (int) $_POST['site_ref_id']);
            $return['status'] = "SUCCESS"; 
            break;
        
        case 'unpublish':
            wp_update_post(array('ID' => (int) $_POST['site_ref_id'], 'post_status' => 'pending'));
            $return['status'] = "SUCCESS"; 
            break;
        
        default:
            $return['status'] = "ERROR"; 
    }
    echo json_encode($return);
    wp_die();
}
