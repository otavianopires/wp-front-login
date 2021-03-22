<?php

require_once WPFL_PATH . 'includes/WpflEnqueueScripts.php';
require_once WPFL_PATH . 'includes/LoginForm.php';
require_once WPFL_PATH . 'includes/ProfileForm.php';

if ( class_exists('WpflEnqueueScripts') ) {
    new WpflEnqueueScripts();
}

if ( class_exists('LoginForm') ) {
    new LoginForm();
}

if ( class_exists('ProfileForm') ) {
    new ProfileForm();
}
