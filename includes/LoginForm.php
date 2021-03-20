<?php

/**
 * Adds Login Form shortcode and user authentication
 */
class LoginForm {

    private $login_url;
    private $redirect_to;

    public function __construct()
    {
        $this->login_url = home_url( '/login' );
        $this->redirect_to = home_url();
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

        add_action( 'init', array( $this, 'redirectToFrontLoginForm' ));
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

        $querystring = array();
        $querystring_url = parse_url($_POST['querystring']);
        parse_str( $querystring_url['query'], $querystring );

        // Validate Username input
        if ( isset( $params['wpfl-username'] )  && empty( $params['wpfl-username'] ) ) {
            wp_send_json_error(
                array(
                    'message' => __('Missing username', WPFL_TEXT_DOMAIN)
                )
            );
        }

        // Validate Password input
        if ( isset($params['wpfl-password']) && empty( $params['wpfl-password'] ) ) {
            wp_send_json_error(
                array(
                    'message' => __('Missing password', WPFL_TEXT_DOMAIN)
                )
            );
        }

        // Try to authenticate user 
        $credentials = array(
            'user_login'    => sanitize_user( $params['wpfl-username'] ),
            'user_password' => $params['wpfl-password'],
            'remember'      => isset( $params['wpfl-rememberme'] ) && $params['wpfl-rememberme'] == 'forever' ? true : false
        );
        $user = wp_signon( $credentials, true );
     
        // Return error authentication failed
        if ( is_wp_error( $user ) ) {
            wp_send_json_error(
                array(
                    'message' => __('The username or password you entered are incorect.', WPFL_TEXT_DOMAIN)
                )
            );
        }

        // Ok, user authenticated. Set current user and auth cookie
        wp_clear_auth_cookie();
        wp_set_current_user( $user->ID );
        wp_set_auth_cookie( $user->ID );

        // Check if there's any redirect to url
        $redirect_to = isset( $querystring['redirect_to'] ) && !empty( $querystring['redirect_to'] ) ? esc_url_raw( $querystring['redirect_to'] ) : $this->redirect_to;

        wp_send_json_success(
            array(
                'message' => 'User logged in',
                'redirect_url' => $redirect_to
            )
        );
    }

    /**
     * Redirect wp-login to Front Login Form
     */
    public function redirectToFrontLoginForm()
    {
        global $pagenow;

        // Check if there's any redirect to url
        $redirect_to = isset( $_GET['redirect_to'] ) && !empty( $querystring['redirect_to'] ) ? esc_url_raw( $_GET['redirect_to'] ) : '';

        // Redirect user if logged in and redirect to contains a valid URL
        if ( is_user_logged_in() && ! empty( $redirect_to ) ) {
            wp_redirect( $redirect_to );
            exit();
        }
        
        // Redirect user from wp-login to Front Login Form
        if ( 'wp-login.php' == $pagenow && sanitize_title( $_GET['action'] ) != 'logout' && sanitize_title( $_GET['action'] ) != 'lostpassword' ) {

            $login_url = $this->login_url;

            if ( !empty($redirect_to) ) {
                $login_url = add_query_arg( 'redirect_to', $redirect_to, $login_url );
            }

            wp_redirect( $login_url  );
            exit();
        }
    }

}