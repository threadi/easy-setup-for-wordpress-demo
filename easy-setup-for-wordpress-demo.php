<?php
/**
 * Plugin Name:       Easy Block Setup for WordPress Example
 * Description:       Example usage of Easy Block Setup for WordPress.
 * Requires at least: 5.0
 * Requires PHP:      8.0
 * Version:           1.0.0
 * Author:            Thomas Zwirner
 * Author URI:        https://www.thomaszwirner.de
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       easy-setup-for-wordpress-demo
 *
 * @package easy-setup-for-wordpress-demo
 */

/**
 * URL bereitstellen unter der Setup startet.
 * URL auf Dashboard verlinken.
 * Setup mit 3 Schritten:
 * 1. Name eingeben.
 * 2. Fortschritt mit künstlicher Pause.
 * 3. Abschluss
 */

/**
 * Add invisible menu in admin.
 *
 * @return void
 */
function esfwd_admin_menu(): void {
    add_submenu_page(
        'page',
        __( 'Setup Demo', 'easy-setup-for-wordpress-demo' ),
        '',
        'manage_options',
        'esfwd_setup_demo',
        'esfwd_setup_demo_page'
    );
}
add_action( 'admin_menu', 'esfwd_admin_menu' );

/**
 * Initialize the setup object.
 *
 * @return void
 */
function esfwd_admin_setup_init(): void {
    // include the necessary setup file.
    require_once plugin_dir_path( __FILE__ ) . 'vendor/threadi/easy-setup-for-wordpress/lib/Setup.php';

    // initialize the setup object.
    $setup_obj = \easySetupForWordPress\Setup::get_instance();
    $setup_obj->init();

    // bail if this is not the setup page.
    $page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
    if( 'esfwd_setup_demo' !== $page ) {
        return;
    }

    // define steps for setup.
    $setup = array(
        1 => array(
            'your_name'              => array(
                'type'                => 'TextControl',
                'label'               => __( 'Your Name', 'easy-setup-for-wordpress-demo' ),
                'help'                => __( 'Help text for this field', 'easy-setup-for-wordpress-demo' ),
                'placeholder'         => __( 'Max Mustermann', 'easy-setup-for-wordpress-demo' ),
                'required'            => true, // true if required for next step
                //'validation_callback' => 'example::validate', // PHP-callback to validate the entry
            ),
        ),
        2 => array(
            'runSetup' => array(
                'type'  => 'ProgressBar',
                'label' => __( 'Demo progressbar', 'easy-setup-for-wordpress-demo' ),
            ),
        )
    );

    // collect configuration for the setup.
    $config = array(
        'name'                  => 'esfwd-setup',
        'title'                 => __( 'Demo Setup', 'easy-setup-for-wordpress-demo' ),
        'steps'                 => $setup,
        'back_button_label'     => __( 'Back', 'easy-setup-for-wordpress-demo' ) . '<span class="dashicons dashicons-undo"></span>',
        'continue_button_label' => __( 'Continue', 'easy-setup-for-wordpress-demo' ) . '<span class="dashicons dashicons-controls-play"></span>',
        'finish_button_label'   => __( 'Completed', 'easy-setup-for-wordpress-demo' ) . '<span class="dashicons dashicons-saved"></span>',
        'skip_button_label'     => __( 'Skip', 'easy-setup-for-wordpress-demo' ) . '<span class="dashicons dashicons-undo"></span>',
    );

    // configure settings for the setup.
    $setup_obj->set_url( trailingslashit( plugin_dir_url( __FILE__ ) ) );
    $setup_obj->set_path( plugin_dir_path( __FILE__ ) );
    $setup_obj->set_texts( array(
        'title_error'      => __( 'Error', 'easy-setup-for-wordpress-demo' ),
        'txt_error_1'      => __( 'The following error occurred:', 'easy-setup-for-wordpress-demo' ),
        'txt_error_2'      => __( 'text after error', 'easy-setup-for-wordpress-demo' ),
    ) );
    $setup_obj->set_config( $config );
}
add_action( 'plugins_loaded', 'esfwd_admin_setup_init' );

/**
 * Show the setup.
 *
 * @return void
 */
function esfwd_setup_demo_page(): void {
    echo \easySetupForWordPress\Setup::get_instance()->display( 'esfwd-setup' );
}

/**
 * Show progress on step 2.
 *
 * @return void
 */
function esfwd_setup_progress(): void {
    update_option( 'esfw_max_steps', 1 );
    update_option( 'esfw_step', 1 );
    update_option( 'esfw_step_label', __( 'Wait for the progress ..', 'easy-setup-for-wordpress-demo' ) );
}
add_action( 'esfw_process', 'esfwd_setup_progress' );

/**
 * Return forward URL if setup has been completed.
 *
 * @param string $config_name The used setup configuration name.
 * @return void
 */
function esfwd_setup_completed( string $config_name ): void {
    // bail if this is not our setup.
    if ( 'esfwd-setup' !== $config_name ) {
        return;
    }

    // Return JSON with forward-URL to dashboard.
    wp_send_json(
        array(
            'forward' => get_admin_url() . 'index.php',
        )
    );
}
add_action( 'esfw_set_completed', 'esfwd_setup_completed' );

/**
 * Add a new dashboard widget.
 *
 * @return void
 */
function esfwd_add_dashboard_widgets(): void {
    wp_add_dashboard_widget(
        'esfwd_dashboard_widget',
        __( 'Easy Setup for WordPress Example', 'easy-setup-for-wordpress-demo' ),
        'esfwd_dashboard',
        null,
        null,
        'normal',
        'high'
    );
}
add_action( 'wp_dashboard_setup', 'esfwd_add_dashboard_widgets' );

/**
 * Show button to start demo setup from dashboard.
 *
 * @return void
 */
function esfwd_dashboard(): void {
    // create URL.
    $url = add_query_arg(
        array(
            'page' => 'esfwd_setup_demo'
        ),
        get_admin_url() . 'admin.php'
    );

    // output.
    echo '<a href="' . esc_url( $url ) . '" class="button button-primary">' . __( 'Start Demo Setup', 'easy-setup-for-wordpress-demo' ) .  '</a><br>';
}