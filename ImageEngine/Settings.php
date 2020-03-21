<?php

namespace ImageEngine;

class Settings
{

    /**
     * register settings
     */
    public static function register_settings()
    {
        register_setting('image_cdn', 'image_cdn', [self::class, 'validate_settings']);
    }


    /**
     * validation of settings
     *
     * @param   array  $data  array with form data
     * @return  array         array with validated values
     */
    public static function validate_settings($data)
    {
        if (!isset($data['relative'])) {
            $data['relative'] = 0;
        }

        if (!isset($data['https'])) {
            $data['https'] = 0;
        }

        $data['url'] = rtrim($data['url'], '/');

        $parts = parse_url($data['url']);
        if (!isset($parts['scheme']) || !isset($parts['host'])) {
            add_settings_error('url', 'url', 'Invalid URL: Missing scheme (<code>http://</code> or <code>https://</code>) or hostname');
        } else {

            // make sure there is a valid scheme
            if (!in_array($parts['scheme'], ['http', 'https'])) {
                add_settings_error('url', 'url', 'Invalid URL: Must begin with <code>http://</code> or <code>https://</code>');
            }

            // make sure the host is resolves
            if (!filter_var($parts['host'], FILTER_VALIDATE_IP)) {
                $ip = gethostbyname($parts['host']);
                if ($ip == $parts['host']) {
                    add_settings_error('url', 'url', 'Invalid URL: Could not resolve hostname');
                }
            }
        }

        $data['path'] = trim($data['path'], '/');
        if (strlen($data['path']) > 0) {
            $data['path'] = '/' . $data['path'];
        }

        return [
            'url'        => esc_url($data['url']),
            'path'       => $data['path'],
            'dirs'       => esc_attr($data['dirs']),
            'excludes'   => esc_attr($data['excludes']),
            'relative'   => (int)$data['relative'],
            'https'      => (int)$data['https'],
            'directives' => self::clean_directives($data['directives']),
            'enabled'    => (int)$data['enabled'],
        ];
    }

    /**
     * clean the ImageEngine Directives
     */
    public static function clean_directives($directives)
    {
        $directives = preg_replace('#.*imgeng=/+?#', '', $directives);
        $directives = trim($directives);

        // ensure there is one leading "/" and none trailing
        $directives = trim($directives, "/");
        $directives = '/' . $directives;
        $directives = rtrim($directives, '/');

        return $directives;
    }


    /**
     * add settings page
     */
    public static function add_settings_page()
    {
        $page = add_options_page('Image CDN', 'Image CDN', 'manage_options', 'image_cdn', [self::class, 'settings_page']);
    }


    /**
     * settings page
     */
    public static function settings_page()
    {
        $options = ImageCDN::get_options();
        $is_runnable = ImageCDN::should_rewrite();
        include __DIR__ . '/../templates/settings.php';
    }
}
