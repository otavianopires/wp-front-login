<div class="wpfl-form-container wpfl-profile-form-container">

    <form id="wpfl-profile-form" action="<?php echo home_url('/wp-admin/admin-post.php'); ?>" method="post">
        <input name="action" type="hidden" id="action" value="wpfl_profile_update" />
        <?php wp_nonce_field( 'wpfl_profile_update', 'wpfl_profile_update_nonce' ) ?>
        <input name="wpfl-user-id" type="hidden" id="wpfl-user-id" value="<?php echo $current_user->ID; ?>" />

        <fieldset>
            <legend><?php _e('Personal Options', WPFL_TEXT_DOMAIN); ?></legend>
            <div class="wpfl-from-group">
                <label for="wpfl-first-name" class="wpfl-login-form-label"><?php _e('First Name', WPFL_TEXT_DOMAIN); ?></label>
                <input type="text" name="wpfl-first-name", id="wpfl-first-name" class="wpfl-input wpfl-input-text" value="<?php the_author_meta( 'first_name', $current_user->ID ); ?>">
            </div>

            <div class="wpfl-from-group">
                <label for="wpfl-last-name" class="wpfl-login-form-label"><?php _e('Last Name', WPFL_TEXT_DOMAIN); ?></label>
                <input type="text" name="wpfl-last-name", id="wpfl-last-name" class="wpfl-input wpfl-input-text" value="<?php the_author_meta( 'last_name', $current_user->ID ); ?>">
            </div>

            <div class="wpfl-from-group">
                <label for="wpfl-email" class="wpfl-login-form-label"><?php _e('Email Address', WPFL_TEXT_DOMAIN); ?></label>
                <input type="email" name="wpfl-email", id="wpfl-email" class="wpfl-input wpfl-input-email" value="<?php the_author_meta( 'user_email', $current_user->ID ); ?>" readonly>
            </div>
        </fieldset>

        <fieldset>
            <legend><?php _e('About Yourself', WPFL_TEXT_DOMAIN); ?></legend>
        
            <div class="wpfl-from-group">
                <label for="wpfl-description" class="wpfl-login-form-label"><?php _e('Description', WPFL_TEXT_DOMAIN); ?></label>
                <textarea class="form-control" name="wpfl-description" id="wpfl-description" rows="4"><?php the_author_meta( 'description', $current_user->ID ); ?></textarea>
            </div>
        </fieldset>

        <fieldset>
            <legend><?php _e('Account Management', WPFL_TEXT_DOMAIN); ?></legend>

            <div class="wpfl-from-group">
                <label for="wpfl-current-password" class="wpfl-login-form-label"><?php _e('Current Password', WPFL_TEXT_DOMAIN); ?></label>
                <input type="password" name="wpfl-current-password", id="wpfl-current-password" class="wpfl-input wpfl-input-password">
            </div>

            <div class="wpfl-from-group">
                <label for="wpfl-pass1" class="wpfl-login-form-label"><?php _e('New Password', WPFL_TEXT_DOMAIN); ?></label>
                <input type="password" name="wpfl-pass1", id="wpfl-pass1" class="wpfl-input wpfl-input-password">
            </div>

            <div class="wpfl-from-group">
                <label for="wpfl-pass2" class="wpfl-login-form-label"><?php _e('Repeat New Password', WPFL_TEXT_DOMAIN); ?></label>
                <input type="password" name="wpfl-pass2", id="wpfl-pass2" class="wpfl-input wpfl-input-password">
            </div>
        </fieldset>

        <div class="wpfl-from-group wpfl-from-group-submit">
            <button class="wpfl-submit wpfl-submit-profile"><?php _e('Update Profile', WPFL_TEXT_DOMAIN); ?></button>
        </div>
    </form>

</div>