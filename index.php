<?php
/**
 * Plugin Name: Page Login Restriction
 * Plugin URI: https://tadeosun.com/plugins/page-login-restriction
 * Description: Restricts pages to logged-in users and shows a custom message or redirects non-logged-in users.
 * Version: 1.0.0
 * Author: Tobi Lekan Adeosun
 * Author URI: https://tadeosun.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: page-login-restriction
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Page_Login_Restriction
{
    // Singleton instance
    private static $instance = null;

    // Plugin options
    private $options;

    // Constructor
    private function __construct()
    {
        // Initialize options
        $this->options = get_option('plr_options', array(
            'redirect_url' => wp_login_url(),
            'restricted_pages' => array(),
            'restriction_type' => 'redirect', // redirect or message
            'custom_message' => 'You must be logged in to view this content. <a href="' . wp_login_url() . '">Click here to log in</a>.',
        ));

        // Hook into WordPress
        add_action('template_redirect', array($this, 'check_page_restriction'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    // Get singleton instance
    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Check if current page is restricted and user is not logged in
    public function check_page_restriction()
    {
        // Only check on single pages
        if (!is_page()) {
            return;
        }

        $current_page_id = get_queried_object_id();

        // Check if current page is restricted and user is not logged in
        if (in_array($current_page_id, $this->options['restricted_pages']) && !is_user_logged_in()) {
            if ($this->options['restriction_type'] === 'redirect') {
                // Redirect to the specified URL
                wp_redirect($this->options['redirect_url']);
                exit;
            } else {
                // Show custom message
                add_filter('the_content', array($this, 'replace_content_with_message'));
                // Remove comments
                add_filter('comments_open', '__return_false');
                add_filter('get_comments_number', '__return_false');
            }
        }
    }

    // Replace page content with custom message
    public function replace_content_with_message($content)
    {
        return wpautop($this->options['custom_message']);
    }

    // Add admin menu page
    public function add_admin_menu()
    {
        add_options_page(
            'Page Login Restriction Settings',
            'Page Restriction',
            'manage_options',
            'page-login-restriction',
            array($this, 'render_admin_page')
        );
    }

    // Register settings
    public function register_settings()
    {
        register_setting('plr_options_group', 'plr_options', array($this, 'sanitize_options'));

        add_settings_section(
            'plr_main_section',
            'Main Settings',
            array($this, 'settings_section_callback'),
            'page-login-restriction'
        );

        add_settings_field(
            'restriction_type',
            'Restriction Type',
            array($this, 'restriction_type_callback'),
            'page-login-restriction',
            'plr_main_section'
        );

        add_settings_field(
            'redirect_url',
            'Redirect URL',
            array($this, 'redirect_url_callback'),
            'page-login-restriction',
            'plr_main_section'
        );

        add_settings_field(
            'custom_message',
            'Custom Message',
            array($this, 'custom_message_callback'),
            'page-login-restriction',
            'plr_main_section'
        );

        add_settings_field(
            'restricted_pages',
            'Restricted Pages',
            array($this, 'restricted_pages_callback'),
            'page-login-restriction',
            'plr_main_section'
        );
    }

    // Sanitize options
    public function sanitize_options($input)
    {
        $new_input = array();

        if (isset($input['redirect_url'])) {
            $new_input['redirect_url'] = esc_url_raw($input['redirect_url']);
        }

        if (isset($input['restriction_type']) && in_array($input['restriction_type'], array('redirect', 'message'))) {
            $new_input['restriction_type'] = $input['restriction_type'];
        }

        if (isset($input['custom_message'])) {
            $new_input['custom_message'] = wp_kses_post($input['custom_message']);
        }

        $new_input['restricted_pages'] = isset($input['restricted_pages']) ? array_map('intval', $input['restricted_pages']) : array();

        return $new_input;
    }

    // Settings section callback
    public function settings_section_callback()
    {
        echo '<p>Configure which pages should be restricted to logged-in users and how to handle non-logged-in users.</p>';
    }

    // Restriction type field callback
    public function restriction_type_callback()
    {
        $restriction_type = isset($this->options['restriction_type']) ? $this->options['restriction_type'] : 'redirect';
        ?>
        <label>
            <input type="radio" name="plr_options[restriction_type]" value="redirect" <?php checked('redirect', $restriction_type); ?> />
            Redirect to another page
        </label>
        <br>
        <label>
            <input type="radio" name="plr_options[restriction_type]" value="message" <?php checked('message', $restriction_type); ?> />
            Show custom message
        </label>
        <p class="description">Choose how to handle non-logged-in users.</p>
        <script>
            jQuery(document).ready(function ($) {
                $('input[name="plr_options[restriction_type]"]').change(function () {
                    if ($(this).val() === 'redirect') {
                        $('#redirect_url_row').show();
                        $('#custom_message_row').hide();
                    } else {
                        $('#redirect_url_row').hide();
                        $('#custom_message_row').show();
                    }
                }).trigger('change');
            });
        </script>
        <?php
    }

    // Redirect URL field callback
    public function redirect_url_callback()
    {
        $redirect_url = isset($this->options['redirect_url']) ? $this->options['redirect_url'] : wp_login_url();
        ?>
        <div id="redirect_url_row">
            <input type="text" id="redirect_url" name="plr_options[redirect_url]" value="<?php echo esc_attr($redirect_url); ?>"
                class="regular-text" />
            <p class="description">Users who are not logged in will be redirected to this URL.</p>
        </div>
        <?php
    }

    // Custom message field callback
    public function custom_message_callback()
    {
        $custom_message = isset($this->options['custom_message']) ? $this->options['custom_message'] : 'You must be logged in to view this content. <a href="' . wp_login_url() . '">Click here to log in</a>.';
        ?>
        <div id="custom_message_row">
            <?php
            wp_editor(
                $custom_message,
                'custom_message',
                array(
                    'textarea_name' => 'plr_options[custom_message]',
                    'textarea_rows' => 5,
                    'media_buttons' => false,
                    'teeny' => true,
                )
            );
            ?>
            <p class="description">This message will be shown instead of the page content. You can include HTML and shortcodes.
            </p>
            <p class="description">Use
                <code>&lt;a href="<?php echo esc_url(wp_login_url()); ?>"&gt;Click here to log in&lt;/a&gt;</code> to add a
                login link.</p>
        </div>
        <?php
    }

    // Restricted pages field callback
    public function restricted_pages_callback()
    {
        // Get all published pages
        $pages = get_pages(array(
            'post_status' => 'publish',
            'sort_column' => 'post_title',
            'sort_order' => 'ASC',
        ));

        if (empty($pages)) {
            echo '<p>No pages found.</p>';
            return;
        }

        echo '<div style="max-height: 300px; overflow-y: auto; padding: 10px; border: 1px solid #ccc;">';

        foreach ($pages as $page) {
            printf(
                '<label style="display: block; margin-bottom: 5px;"><input type="checkbox" name="plr_options[restricted_pages][]" value="%d" %s /> %s</label>',
                $page->ID,
                in_array($page->ID, $this->options['restricted_pages']) ? 'checked="checked"' : '',
                esc_html($page->post_title)
            );
        }

        echo '</div>';
        echo '<p class="description">Select the pages that should be restricted to logged-in users.</p>';
    }

    // Render admin page
    public function render_admin_page()
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('plr_options_group');
                do_settings_sections('page-login-restriction');
                submit_button('Save Settings');
                ?>
            </form>
        </div>
        <?php
    }
}

// Initialize the plugin
function page_login_restriction_init()
{
    Page_Login_Restriction::get_instance();
}
add_action('plugins_loaded', 'page_login_restriction_init');