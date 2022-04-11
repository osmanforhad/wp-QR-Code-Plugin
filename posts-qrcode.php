<?php

/**
 * Plugin Name:       Posts Qr code Plugin
 * Plugin URI:        https://osmanforhad.net/plugins/practice/
 * Description:       WordPress Posts to Qr code Plugin Plugin by osman forhad. Which will generate 
 * and Display QR code under every WorPress Post
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.1
 * Author:            osman forhad
 * Author URI:        https://author.osmanforhad.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       posts-qrcode
 * Domain Path:       /languages/
 */

//__CallBack function for Plugin Activation__//
function posts_qrcode_activation_hook()
{
}
//Action Hook for Plugin Activation
register_activation_hook(__FILE__, "posts_qrcode_activation_hook");


//__CallBack function for Plugin DeActivation__//
function posts_qrcode_deactivation_hook()
{
}
//Action Hook for Plugin DeActivation
register_deactivation_hook(__FILE__, "posts_qrcode_deactivation_hook");


//__CallBack function for Plugin TextDomain__//
function posts_qrcode_load_text_domain()
{
    load_plugin_textdomain('posts-qrcode', false, dirname(__FILE__) . "/languages");
}
//Action Hook for Plugin TextDomain
add_action("plugin_loaded", "posts_qrcode_load_text_domain");


//__CallBack function for Display QR code under every WordPress Post Content__//
function pqrc_display_qr_code($content)
{
    //get current post
    $current_post_id = get_the_ID();
    //get url of the current post
    $current_post_url = urlencode(get_the_permalink($current_post_id));
    //get current post title
    $current_post_title = get_the_title($current_post_id);
    //get current post type
    $current_post_type = get_post_type($current_post_id);

    /**
     * Post Type Check
     */
    $excluded_post_type = apply_filters(
        'pqrc_excluded_post_types',
        array()
    );
    if (in_array($current_post_type, $excluded_post_type)) {
        return $content;
    }

    //__Retrieve QR Code Hight With from wp_options Table
    $Qr_height = get_option('pqrc_height');
    $Qr_width = get_option('pqrc_width');
    //check height width is exists or not
    $height = $Qr_height ? $Qr_height : 100;
    $width = $Qr_width ? $Qr_width : 100;

    /**
     * Setup QR image Dimension
     */
    $qr_image_dimension = apply_filters(
        'pqrc_qrcode_dimension',
        "{$width}x{$height}"
    );

    /**
     * Setup QR image Attributes
     */
    $image_attributes = apply_filters(
        'pqrc_image_attributes',
        null
    );


    //QR image src
    $image_src = sprintf(
        'https://api.qrserver.com/v1/create-qr-code/?size=%s&data=%s',
        $qr_image_dimension,
        $current_post_url
    );
    //output of the plugin
    $content .= sprintf(
        "<div class='qrcode'><img %s src='%s', alt='%s' /></div>",
        $image_attributes,
        $image_src,
        $current_post_title
    );
    return $content;
}

//Filter Hook for get wp content
add_filter('the_content', 'pqrc_display_qr_code');

/**
 *Settings functionality for
 *Plugin
 */
//_Callback Function for Plugin Height Width Settings in wp_options Table
function pqrc_settings_init()
{
    //hook for add plugin Settings Section
    add_settings_section(
        'pqrc_section',
        __('Posts QR Code Section from Posts QR Code Plugin', 'posts-qrcode'),
        'pqrc_section_callback',
        'reading'
    );


    //hook for add plugin Settings Field Height Width
    add_settings_field(
        'pqrc_height',
        __('QR Code Height', 'posts-qrcode'),
        'pqrc_display_field',
        'reading',
        'pqrc_section',
        array('pqrc_height')
    );
    add_settings_field(
        'pqrc_width',
        __('QR Code Width', 'posts-qrcode'),
        'pqrc_display_field',
        'reading',
        'pqrc_section',
        array('pqrc_width')
    );

    add_settings_field(
        'pqrc_select',
        __('DropDown Select', 'posts-qrcode'),
        'pqrc_display_select_field',
        'reading',
        'pqrc_section'
    );

    add_settings_field(
        'pqrc_checkbox',
        __('Select CheckBox', 'posts-qrcode'),
        'pqrc_display_checkbox_field',
        'reading',
        'pqrc_section'
    );


    //Register Custom Field for QR code plugin
    register_setting(
        'reading',
        'pqrc_height',
        array('sanitize_callback' => 'esc_attr')
    );

    register_setting(
        'reading',
        'pqrc_width',
        array('sanitize_callback' => 'esc_attr')
    );

    register_setting(
        'reading',
        'pqrc_select',
        array('sanitize_callback' => 'esc_attr')
    );

    register_setting(
        'reading',
        'pqrc_checkbox'
    );
}

//__Callback function for Display Settings Section__//
function pqrc_section_callback()
{
    echo "<p>" . __('Settings for Posts QR Code Plugin', 'posts-qrcode') . "</p>";
}

//__Callback function for Display input box Settings Fields__//
function pqrc_display_field($args)
{
    $options = get_option($args[0]);
    printf(
        "<input type='text' id='%s' name='%s' value='%s'/>",
        $args[0],
        $args[0],
        $options
    );
}

//__Callback function for Display Dropdown field in Settings area__//
function pqrc_display_select_field()
{
    $option = get_option('pqrc_select');
    $Districts = array(
        'None',
        'Dhaka',
        'Chittagong',
        'Khulna',
        'Dinajpur',
        'Feni',
        'Jessor',
        'Rajsahi'
    );
    printf(
        '<select id="%s" name="%s">',
        'pqrc_select',
        'pqrc_select'
    );
    foreach ($Districts as $District) {
        $selected = '';
        if ($option == $District) {
            $selected = 'selected';
        }
        printf(
            '<option value="%s" %s>%s</option>',
            $District,
            $selected,
            $District
        );
    }
    echo '</select>';
}

//___Callback function for Display Checkbox Field__//
function pqrc_display_checkbox_field()
{
    $option = get_option('pqrc_checkbox');
    $Districts = array(
        'Dhaka',
        'Chittagong',
        'Khulna',
        'Dinajpur',
        'Feni',
        'Jessor',
        'Rajsahi'
    );

    foreach ($Districts as $District) {
        $selected = '';
        if (is_array($option) && in_array($District, $option)) {
            $selected = 'checked';
        }

        printf(
            '<input type="checkbox" name="pqrc_checkbox[]" value="%s" %s />%s <br/>',
            $District,
            $selected,
            $District
        );
    }
}

//Action hook for plugin settings
add_action("admin_init", "pqrc_settings_init");
