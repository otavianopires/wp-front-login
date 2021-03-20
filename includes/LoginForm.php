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
        add_shortcode( 'wpfl-login-form', array( $this, 'loginFormShortcode') );
    }

    /**
     * Output Login Form template
     */
    public function loginFormShortcode( $atts )
    {
        ob_start();
        include_once WPFL_PATH . 'templates/login-form.php';
        $output = ob_get_clean();
        return $output;
    }

}