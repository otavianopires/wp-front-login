<div class="wpfl-login-form-container">

    <div class="wpfl-alert"></div>

    <form id="wpfl-login-form">
        <div class="wpfl-from-group">
            <label for="wpfl-username" class="wpfl-login-form-label"><?php _e('Username or Email Address', WPFL_TEXT_DOMAIN); ?></label>
            <input type="text" name="wpfl-username", id="wpfl-username" class="wpfl-input wpfl-input-username">
        </div>
        <div class="wpfl-from-group">
            <label for="wpfl-password" class="wpfl-login-form-label"><?php _e('Password', WPFL_TEXT_DOMAIN); ?></label>
            <input type="password" name="wpfl-password", id="wpfl-password" class="wpfl-input wpfl-input-password">
        </div>
        <div class="wpfl-from-group wpfl-from-group-submit">
            <div class="wpfl-rememberme-container">
                <input type="checkbox" id="wpfl-rememberme" name="wpfl-rememberme" value="forever">
                <label for="wpfl-rememberme"> <?php _e('Remember Me', WPFL_TEXT_DOMAIN); ?></label>
            </div>
            <button class="wpfl-submit"><?php _e('Login', WPFL_TEXT_DOMAIN); ?></button>
        </div>
    </form>

</div>