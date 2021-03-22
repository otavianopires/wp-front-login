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
    }
}