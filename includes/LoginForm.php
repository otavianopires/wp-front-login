<?php

/**
 * Adds Login Form shortcode and user authentication
 */
class LoginForm {

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
        add_shortcode( 'wpfl-login-form', array( $this, 'loginFormShortcode') );
    }

    /**
     * Enqueue Styles and Scripts
     */
    public function enqueueStylesAndScripts()
    {
        wp_register_style('wpfl-login-form', WPFL_URL . 'assets/css/wpfl-login-form.css', 'style', WPFL_VERSION);
    }

    /**
     * Output Login Form template
     */
    public function loginFormShortcode( $atts )
    {
        wp_enqueue_style('wpfl-login-form');

        ob_start();
        include_once WPFL_PATH . 'templates/login-form.php';
        $output = ob_get_clean();
        return $output;
    }

}