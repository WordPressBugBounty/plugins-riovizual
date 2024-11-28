<?php
// all modal html css and js code
function feedback_html_content(){
    ?>
        <div class="rv-feedback-overlay" id="rv-feedback-modal" style="display:none">
            <div class="rv-feedback-popup">
                <form id="rv-feedback-form">
                    <div class="rv-feedback-header">
                        <img src="https://riovizual.com/wp-content/uploads/2023/12/Favicon-Riovizaul.png" />
                        <h2><?php echo __( 'Quick Feedback', 'riovizual' );?></h2>
                    </div>
                    <div class="rv-feedback-body">
                        <h3><?php echo __( 'If you have a moment, please let us know why you are deactivating Riovizual:', 'riovizual' );?></h3>
                        <div class="rv-feedback-options">
                            <div>
                                <input type="radio" id="no_longer_need" name="rv-feedback-op" value="I no longer need the plugin">
                                <label for="no_longer_need"><?php echo __( 'I no longer need the plugin', 'riovizual' );?></label>
                            </div>
                            <div>
                                <input type="radio" id="short_perid" name="rv-feedback-op" value="I only needed the plugin for a short period">
                                <label for="short_perid"><?php echo __( 'I only needed the plugin for a short period', 'riovizual' );?></label>
                            </div>
                            <div>
                                <input type="radio" id="broke_website" name="rv-feedback-op" value="The plugin broke my site">
                                <label for="broke_website"><?php echo __( 'The plugin broke my site', 'riovizual' );?></label>
                            </div>
                            <div>
                                <input type="radio" id="sudden_stop" name="rv-feedback-op" value="I couldn't get the plugin to work">
                                <label for="sudden_stop"><?php echo __( "I couldn't get the plugin to work", 'riovizual' );?></label>
                            </div>

                            <div>
                                <input type="radio" id="found_better_plugin" name="rv-feedback-op" value="I found a better plugin">
                                <label for="found_better_plugin"><?php echo __( 'I found a better plugin', 'riovizual' );?></label>
                            </div>
                            <div class="better_plugin rv-d-none" id="better_plugin_form">
                                <input type="text" placeholder="Please share which plugin" name="better_plugin_name" value="" id="better_plugin_input">
                            </div>

                            <div>
                                <input type="radio" id="temporary" name="rv-feedback-op" value="It's a temporary deactivation">
                                <label for="temporary"><?php echo __( "It's a temporary deactivation", 'riovizual' );?></label>
                            </div>

                            <div>
                                <input type="radio" id="other" name="rv-feedback-op" value="Other">
                                <label for="other"><?php echo __( 'Other', 'riovizual' );?></label>
                            </div>
                            <div class="other_feedback rv-d-none" id="other_feedback_form">
                                <input type="text" placeholder="Please share the reason" name="other_feedback" value="" id="rv-other-feedback">
                            </div>

                            <input type="hidden" id="rv-current-version" name="rv-current-version" value="<?php echo RIO_VIZUAL_VERSION;?>">
                        </div>
                    </div>
                    <div class="rv-feedback-footer">
                        <div>
                            <button class="rv-feedback-submit-btn" type="submit" id="rv-submit-feedback-btn"><?php echo __( 'Skip & Deactivate', 'riovizual' );?></button>
                            <button class="rv-feedback-close" href="#" id="rv-cancel-feedback-btn"><?php echo __( 'Cancel', 'riovizual' );?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    <?php
}
add_action('admin_footer', 'rio_viz_feedback_modal');

function rio_viz_feedback_modal() {
    global $pagenow;

    // Check if we are on the plugins.php page
    if ( $pagenow == 'plugins.php' ) {
        feedback_html_content(); // Call the function that contains your modal HTML
    }
}