<?php

/**
 * Enqueue scripts and styles
 */
class WpflEnqueueScripts {

    public function __construct()
    {
        $this->init();
    }

    /**
     * Add Actions and Filters
     */
    private function init()
    {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueueStylesAndScripts') );
    }

    /**
     * Enqueue Styles and Scripts
     */
    public function enqueueStylesAndScripts()
    {
        wp_register_style( 'wpfl-form-style', WPFL_URL . 'assets/css/wpfl-form.css', 'style', WPFL_VERSION );

        wp_register_script( 'wpfl-login-form-script', WPFL_URL . 'assets/js/wpfl-login-form.js', array( 'jquery' ), WPFL_VERSION, true );

        wp_localize_script( 'wpfl-login-form-script', 'wpfl_login_form_obj',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'wpfl-login-form-nonce' )
            )
        );
    }

}