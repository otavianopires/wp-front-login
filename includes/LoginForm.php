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

        add_action( 'wp_ajax_nopriv_wpfl_login_form', array( $this, 'processLogin' ) );
        add_action( 'wp_ajax_wpfl_login_form', array( $this, 'processLogin' ) );
    }

    /**
     * Enqueue Styles and Scripts
     */
    public function enqueueStylesAndScripts()
    {
        wp_register_style( 'wpfl-login-form-style', WPFL_URL . 'assets/css/wpfl-login-form.css', 'style', WPFL_VERSION );

        wp_register_script( 'wpfl-login-form-script', WPFL_URL . 'assets/js/wpfl-login-form.js', array( 'jquery' ), WPFL_VERSION, true );

        wp_localize_script( 'wpfl-login-form-script', 'wpfl_login_form_obj',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'wpfl-login-form-nonce' )
            )
        );
    }

    /**
     * Output Login Form template
     */
    public function loginFormShortcode( $atts )
    {
        wp_enqueue_style( 'wpfl-login-form-style' );
        wp_enqueue_script( 'wpfl-login-form-script' );

        ob_start();
        include_once WPFL_PATH . 'templates/login-form.php';
        $output = ob_get_clean();
        return $output;
    }

    /**
     * Process User Login 
     */
    public function processLogin()
    {
        check_ajax_referer('wpfl-login-form-nonce');
        
        $params = array();
        parse_str( $_POST['form_data'], $params );

        if ( isset( $params['wpfl-username'] ) && empty( $params['wpfl-username'] ) ) {
            wp_send_json_error(
                array(
                    'message' => 'Missing username'
                )
            );
        }

        if ( isset($params['wpfl-password']) && empty( $params['wpfl-password'] ) ) {
            wp_send_json_error(
                array(
                    'message' => 'Missing password'
                )
            );
        }

        $credentials = array(
            'user_login'    => sanitize_user( $params['wpfl-username'] ),
            'user_password' => $params['wpfl-password'],
            'remember'      => isset( $params['wpfl-rememberme'] ) && $params['wpfl-rememberme'] == 'forever' ? true : false
        );
     
        $user = wp_signon( $credentials, true );
     
        if ( is_wp_error( $user ) ) {
            wp_send_json_error(
                array(
                    'message' => $user->get_error_message()
                )
            );
        }

        wp_clear_auth_cookie();
        wp_set_current_user( $user->ID );
        wp_set_auth_cookie( $user->ID );

        wp_send_json_success(
            array(
                'message' => 'All good!',
                'form_data' => $params,
                'redirect_url' => home_url()
            )
        );
    }

}