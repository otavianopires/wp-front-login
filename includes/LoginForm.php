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
        add_shortcode( 'wpfl-login-form', array( $this, 'loginFormShortcode') );

        add_action( 'wp_ajax_nopriv_wpfl_login_form', array( $this, 'processLogin' ) );
        add_action( 'wp_ajax_wpfl_login_form', array( $this, 'processLogin' ) );

        add_action( 'init', array( $this, 'redirectToFrontLoginForm' ));
        add_action( 'wp_logout', array( $this, 'logoutRedirect') );
    }

    /**
     * Output Login Form template
     */
    public function loginFormShortcode( $atts )
    {
        wp_enqueue_style( 'wpfl-form-style' );
        wp_enqueue_script( 'wpfl-login-form-script' );

        if ( ! is_user_logged_in() ) {
            ob_start();
            include_once WPFL_PATH . 'templates/login-form.php';
            $output = ob_get_clean();
            return $output;
        } else {
            echo '<p>';
            wp_loginout( home_url() );
            echo '</p>';
        }
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
        $errors = array();
        if ( isset( $params['wpfl-username'] )  && empty( $params['wpfl-username'] ) ) {
            array_push( $errors, __('The username field is empty.', WPFL_TEXT_DOMAIN));
        }

        // Validate Password input
        if ( isset($params['wpfl-password']) && empty( $params['wpfl-password'] ) ) {
            array_push( $errors, __('The password field is empty.', WPFL_TEXT_DOMAIN));
        }
        
        if ( !empty( $errors ) ) {
            wp_send_json_error(
                array(
                    'errors' => $errors
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
                    'errors' => array( __( 'The username or password you entered are incorect.', WPFL_TEXT_DOMAIN) )
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
        if ( $pagenow == "wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET' ) {

            $action = isset($_GET['action']) ? $_GET['action'] : false;
            $allowed_actions = array('logout', 'lostpassword', 'rp', 'resetpass');

            if ( $action || in_array( $action, $allowed_actions) ) {
                return;
            }

            $redirect_to = isset( $_GET['redirect_to'] ) ? esc_url( $_GET['redirect_to'] ) : false;

            if ( $redirect_to ) {
                $query_args = array( 'redirect_to' => $redirect_to );
                $login_url = add_query_arg( $query_args, $this->login_url );
            
                wp_redirect( $login_url );
                exit;
            }
            
            wp_redirect( $this->login_url );
            exit;
        }
        
    }

    /**
     * Redirect user after logout
     */
    public function logoutRedirect()
    {
        wp_redirect( $this->login_url );
        exit;
    }

}