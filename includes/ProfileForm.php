<?php

/**
 * Adds custom Profile Form with shortcode
 */
class ProfileForm {

    private $profile_url;
    private $redirect_to;

    public function __construct()
    {
        $this->profile_url = home_url( '/profile' );
        $this->redirect_to = home_url( '/login' );
        $this->init();
    }

    /**
     * Add Actions and Filters
     */
    private function init()
    {
        add_shortcode( 'wpfl-profile-form', array( $this, 'profileFormShortcode') );

        add_action( 'admin_post_wpfl_profile_update', array( $this, 'processProfileUpdate') );

        add_action( 'template_redirect', array( $this, 'profileFormRedirect' ) );
    }

    /**
     * Output Profile Form template
     */
    public function profileFormShortcode( $atts )
    {
        wp_enqueue_style( 'wpfl-form-style' );

        $current_user = wp_get_current_user();
        if ($current_user) {
            $first_name = get_the_author_meta( 'first_name', $current_user->ID ) ?? '';
            $last_name = get_the_author_meta( 'last_name', $current_user->ID ) ?? '';
            $user_email = get_the_author_meta( 'user_email', $current_user->ID ) ?? '';
            $description = get_the_author_meta( 'description', $current_user->ID ) ?? '';
        }

        // Error messages
        $errors_list = array();
        if ( isset( $_GET['errors'] ) ) {
            $error_codes = explode( ',', $_GET['errors'] );
        
            foreach ( $error_codes as $error_code ) {
                 array_push($errors_list, $this->errorMessage($error_code));
            }
        }

        ob_start();
        include_once WPFL_PATH . 'templates/profile-form.php';
        $output = ob_get_clean();
        return $output;
    }
    
    /**
     * Process profile update
     */
    public function processProfileUpdate() {

        if ( !isset($_POST['wpfl_profile_update_nonce']) || ! wp_verify_nonce( $_POST['wpfl_profile_update_nonce'], 'wpfl_profile_update' ) ) {
            $query_args = array( 'profile' => 'invalid' );
            $redirect_to = add_query_arg( $query_args, $this->profile_url );
            wp_redirect( $redirect_to );
            exit;
        }
        
        $current_user = wp_get_current_user();

        if ($current_user && isset( $_POST['wpfl-user-id'] ) && $current_user->ID == $_POST['wpfl-user-id']) {
            
            if ( isset( $_POST['wpfl-first-name'] ) ) {
                update_user_meta( $current_user->ID, 'first_name', sanitize_text_field( $_POST['wpfl-first-name'] ) );
            }
    
            if ( isset( $_POST['wpfl-last-name'] ) ) {
                update_user_meta($current_user->ID, 'last_name', sanitize_text_field( $_POST['wpfl-last-name'] ) );
            }
    
            if ( isset( $_POST['wpfl-description'] ) ) {
                update_user_meta($current_user->ID, 'description', sanitize_text_field( $_POST['wpfl-description'] ) );
            }
    
            if ( isset( $_POST['wpfl-pass1'] ) && !empty( $_POST['wpfl-pass1'] ) ) {
                if ( empty( $_POST['wpfl-current-password']) ) {
                    // Passwords don't match
                    $query_args = array( 'errors' => 'currentPasswordEmpty' );
                    $redirect_url = add_query_arg( $query_args, $this->profile_url );    
                    wp_redirect( $redirect_url );
                    exit;
                }

                if (! wp_check_password( $_POST['wpfl-current-password'], $current_user->data->user_pass, $current_user->ID )) {
                    // Passwords don't match
                    $query_args = array( 'errors' => 'currentPasswordMismatch' );
                    $redirect_url = add_query_arg( $query_args, $this->profile_url );    
                    wp_redirect( $redirect_url );
                    exit;
                }

                if ( $_POST['wpfl-pass1'] != $_POST['wpfl-pass2'] ) {
                    // Passwords don't match 
                    $query_args = array( 'errors' => 'passwordResetMismatch' );
                    $redirect_url = add_query_arg( $query_args, $this->profile_url );    
                    wp_redirect( $redirect_url );
                    exit;
                }
    
                if ( empty( $_POST['wpfl-pass1'] ) ) {
                    // Password is empty 
                    $query_args = array( 'errors' => 'passwordResetEmpty' );
                    $redirect_url = add_query_arg( $query_args, $this->profile_url );  
                    wp_redirect( $redirect_url );
                    exit;
                }
  
                if ( !$this->checkPasswordStrength($_POST['wpfl-pass1']) ) {
                    // Password is weak
                    $query_args = array( 'errors' => 'passwordStrength' );
                    $redirect_url = add_query_arg( $query_args, $this->profile_url );    
                    wp_redirect( $redirect_url );
                    exit;
                }
    
                // Parameter checks OK, reset password
                wp_set_password($_POST['wpfl-pass1'], $current_user->ID);

                // Set current user and cookies after password update
                $this->loginUser($current_user->ID);
            }
        
            $redirect_url = add_query_arg( 'profile', 'updated', $this->profile_url );    
            wp_redirect( $redirect_url );
            exit;
        }
    }

    /**
     * Check password
     */
    public function checkPasswordStrength($password) {
        $uppercase    = preg_match('@[A-Z]@', $password);
        $lowercase    = preg_match('@[a-z]@', $password);
        $number       = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);

        if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 12 ) {
            return false;
        }

        return true;
    }

    /**
     * Login User
     */
    private function loginUser($user_id)
    {
        wp_clear_auth_cookie();
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);
    }

    public function errorMessage( $error_code ) {
        switch ( $error_code ) {
            case 'expiredkey':
            case 'invalidkey':
                return __( 'The password reset link you used is not valid anymore.' );
             
            case 'currentPasswordEmpty':
                return __( "You need to enter your current password." );
             
            case 'currentPasswordMismatch':
                return __( "The current password you entered doesn't match." );
             
            case 'passwordResetMismatch':
                return __( "The two passwords you entered don't match." );
                 
            case 'passwordResetEmpty':
                return __( "Sorry, we don't accept empty passwords." );
                 
            case 'passwordStrength':
                return __( "Password must be at least twelve characters long, use upper and lower case letters, numbers, and symbols special characters." );

            default:
                break;
        }
         
        return __( 'An unknown error occurred. Please try again later.' );
    }

    /**
     * Redirect non logged in users from the profile page
     */
    public function profileFormRedirect() {
        if ( is_page('profile') && !is_user_logged_in() ) {

            $query_args = array( 'redirect_to' => $this->profile_url );
            $redirect_to = add_query_arg( $query_args, $this->redirect_to );
            
            wp_redirect( $redirect_to );
            exit;
        }
     
    }
}