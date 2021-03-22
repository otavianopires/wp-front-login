<?php

/**
 * Adds custom Profile Form with shortcode
 */
class ProfileForm {

    private $profile_url;

    public function __construct()
    {
        $this->profile_url = home_url( '/profile' );
        $this->init();
    }

    /**
     * Add Actions and Filters
     */
    private function init()
    {
        add_shortcode( 'wpfl-profile-form', array( $this, 'profileFormShortcode') );
    }

    /**
     * Output Profile Form template
     */
    public function profileFormShortcode( $atts )
    {

        ob_start();
        include_once WPFL_PATH . 'templates/profile-form.php';
        $output = ob_get_clean();
        return $output;
    }
}