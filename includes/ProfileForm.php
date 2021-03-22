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
        add_action( 'template_redirect', array( $this, 'profileFormRedirect' ) );
    }

    /**
     * Output Profile Form template
     */
    public function profileFormShortcode( $atts )
    {
        wp_enqueue_style( 'wpfl-login-form-style' );

        $current_user = wp_get_current_user();

        ob_start();
        include_once WPFL_PATH . 'templates/profile-form.php';
        $output = ob_get_clean();
        return $output;
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