<?php

namespace ImageEngine;

class ImageCDN
{

    /**
     * Static constructor
     */
    public static function instance()
    {
        new self();
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        // Rewriter hook
        add_action('template_redirect', [self::class, 'handle_rewrite_hook']);

        // Rewrite rendered content in REST API
        add_filter('the_content', [self::class, 'rewrite_the_content'], 100);

        // Resource hints
        add_action('wp_head', [self::class, 'add_head_tags'], 0);

        // Hooks
        add_action('admin_init', [self::class, 'register_textdomain']);
        add_action('admin_init', [Settings::class, 'register_settings']);
        add_action('admin_menu', [Settings::class, 'add_settings_page']);
        add_action('admin_footer', [Settings::class, 'register_test_config']);
        add_action('wp_ajax_image_cdn_test_config', [Settings::class, 'test_config']);
        add_filter('plugin_action_links_' . IMAGE_CDN_BASE, [self::class, 'add_action_link']);
    }

    /**
     * Add meta tags for Client Hints and Preconnect Resource Hint.
     */
    public static function add_head_tags()
    {
        if (!self::should_rewrite()) {
            return;
        }

        // Add client hints
        echo '    <meta http-equiv="Accept-CH" content="DPR, Viewport-Width, Width, Save-Data">' . "\n";

        // Add resource hints
        $options = self::get_options();
        $host = parse_url($options['url'], PHP_URL_HOST);
        if (!empty($host)) {
            echo '    <link rel="preconnect" href="//' . $host . '">' . "\n";
        }
    }

    /**
     * Add action links
     *
     * @param   array  $data  already existing links
     * @return  array  $data  extended array with links
     */
    public static function add_action_link($data)
    {
        // check permission
        if (!current_user_can('manage_options')) {
            return $data;
        }

        return array_merge(
            $data,
            [
                sprintf(
                    '<a href="%s">%s</a>',
                    add_query_arg(['page' => 'image_cdn'], admin_url('options-general.php')),
                    __("Settings")
                ),
            ]
        );
    }

    /**
     * Run uninstall hook
     */
    public static function handle_uninstall_hook()
    {
        delete_option('image_cdn');
    }

    public static function default_options()
    {
        $url = self::get_url_path();
        return [
            'url'        => $url['base'],
            'path'       => $url['path'],
            'dirs'       => implode(',', [WP_CONTENT_DIR, WPINC]),
            'excludes'   => '.php',
            'relative'   => true,
            'https'      => false,
            'directives' => '',
            'enabled'    => false,
        ];
    }

    /**
     * Run activation hook
     */
    public static function handle_activation_hook()
    {
        add_option('image_cdn', self::default_options());
    }

    /**
     * Check plugin requirements
     */
    public static function image_cdn_requirements_check()
    {
        // WordPress version check
        if (version_compare($GLOBALS['wp_version'], IMAGE_CDN_MIN_WP . 'alpha', '<')) {
            show_message(
                sprintf(
                    '<div class="error"><p>%s</p></div>',
                    sprintf(
                        __("The Image CDN plugin is optimized for WordPress %s. Please disable the plugin or upgrade your WordPress installation (recommended).", "image-cdn"),
                        IMAGE_CDN_MIN_WP
                    )
                )
            );
        }
    }

    /**
     * Register textdomain
     */
    public static function register_textdomain()
    {
        load_plugin_textdomain('image-cdn', false, 'image-cdn/lang');
    }

    /**
     * Return plugin options
     *
     * @return  array  $diff  data pairs
     */
    public static function get_options()
    {
        return wp_parse_args(get_option('image_cdn'), self::default_options());
    }

    /**
     * Split the WP home URL into base URL and path components
     */
    public static function get_url_path()
    {
        $url = get_option('home');
        $base_url = $url;
        $path = '';

        if (preg_match('#^(https?://[^/]+)(/.*)$#', $url, $matches)) {
            $base_url = $matches[1];
            $path = $matches[2];
        }

        return [
            'url' => $url,
            'base' => $base_url,
            'path' => $path,
        ];
    }

    /**
     * Return new rewriter
     */
    public static function get_rewriter()
    {
        $options = self::get_options();
        $excludes = array_map('trim', explode(',', $options['excludes']));

        return new Rewriter(
            get_option('home'),
            $options['url'],
            $options['path'],
            $options['dirs'],
            $excludes,
            $options['relative'],
            $options['https'],
            $options['directives']
        );
    }

    /**
     * Run rewrite hook
     */
    public static function handle_rewrite_hook()
    {
        if (!self::should_rewrite()) {
            return false;
        }

        $rewriter = self::get_rewriter();
        ob_start([$rewriter, 'rewrite']);
    }

    /**
     * Rewrite html content
     */
    public static function rewrite_the_content($html)
    {
        if (!self::should_rewrite()) {
            return false;
        }

        $rewriter = self::get_rewriter();
        return $rewriter->rewrite($html);
    }

    /**
     * Returns true if the content should be rewritten
     */
    public static function should_rewrite()
    {
        $options = self::get_options();

        if (!$options['enabled']) {
            return false;
        }

        if ($options['url'] == '') {
            return false;
        }

        return true;
    }
}
