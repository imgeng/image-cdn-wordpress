<?php

namespace ImageEngine;

class Rewriter
{
    /**
     * WordPress installation URL
     */
    public $blog_url;
    
    /**
     * CDN URL
     */
    public $cdn_url;

    /**
     * Path / subdirectory of the WordPress installation, if not '/'
     */
    public $path;

    /**
     * Included directories
     */
    public $dirs;
 
    /**
     * Excludes
     */
    public $excludes = [];
 
    /**
     * Use CDN on relative paths
     */
    public $relative = false;
 
    /**
     * Use CDN on HTTPS
     */
    public $https = false;

    /**
     * ImageEngine Directives
     */
    public $directives;

    /**
     * Constructor
     */
    public function __construct(
        $blog_url,
        $cdn_url,
        $path,
        $dirs,
        array $excludes,
        $relative,
        $https,
        $directives
    ) {
        $this->blog_url       = $blog_url;
        $this->cdn_url        = $cdn_url;
        $this->path           = $path;
        $this->dirs           = $dirs;
        $this->excludes       = $excludes;
        $this->relative       = $relative;
        $this->https          = $https;
        $this->directives     = $directives;
    }


    /**
     * Exclude assets that should not be rewritten
     * @param   string  $asset  current asset
     * @return  boolean  true if need to be excluded
     */
    protected function exclude_asset($asset)
    {
        // excludes
        foreach ($this->excludes as $exclude) {
            if (!!$exclude && stristr($asset, $exclude) != false) {
                return true;
            }
        }
        return false;
    }


    /**
     * Relative url
     * @param   string  $url a full url
     * @return  string  protocol relative url
     */
    protected function relative_url($url)
    {
        return substr($url, strpos($url, '//'));
    }


    /**
     * Rewrite url
     * @param   string  $asset  current asset
     * @return  string  updated url if not excluded
     */
    protected function rewrite_url($asset)
    {
        $asset_url = $asset[0];

        if ($this->exclude_asset($asset_url)) {
            return $asset_url;
        }

        // Don't rewrite if in preview mode
        if (is_admin_bar_showing()
                && array_key_exists('preview', $_GET)
                && $_GET['preview'] == 'true') {
            return $asset_url;
        }

        $blog_url = $this->relative_url($this->blog_url);
        $subst_urls = ['http:'.$blog_url];

        // rewrite both http and https URLs if we ticked 'enable CDN for HTTPS connections'
        if ($this->https) {
            $subst_urls[] = 'https:'.$blog_url;
        }

        // add ImageEngine directives, if any
        $asset_url = $this->add_directives($asset_url);

        // prepend the path in case this installation is not at /
        if ($this->path != '') {
            $asset_url = '/' . trim($this->path, '/') . $asset_url;
        }

        // is it a relative-protocol URL?
        if (strpos($asset_url, '//') === 0) {
            return str_replace($blog_url, $this->cdn_url, $asset_url);
        }

        // check if not a relative path
        if (!$this->relative || strstr($asset_url, $blog_url)) {
            return str_replace($subst_urls, $this->cdn_url, $asset_url);
        }

        // relative URL
        return $this->cdn_url . $asset_url;
    }

    protected function add_directives($url)
    {
        // No directives, don't do anything
        if (trim($this->directives) == '') {
            return $url;
        }

        // No query string, add ours
        if (strpos($url, '?') === false) {
            return $url . '?imgeng=' . $this->directives;
        }
        
        // If there are already some directives, add the new ones
        if (strpos($url, 'imgeng=') !== false) {
            return preg_replace('#(\?.*?imgeng=)/?#', '$1' . $this->directives . '/', $url);
        } else {
        }

        return $url . '&imgeng=' . $this->directives;
    }

    /**
     * Get directory scope
     * @return  string  directory scope
     */

    protected function get_dir_scope()
    {
        $dirs = trim($this->dirs, ' ,');
        if ($dirs == '') {
            $default = ImageCDN::default_options();
            $dirs = $default['dirs'];
        }

        $input = explode(',', $dirs);

        return implode('|', array_map(function ($in) {
            $in = trim($in);
            $in = preg_quote($in);
            return $in;
        }, $input));
    }

    /**
     * Rewrite URL
     * @param   string  $html  current raw HTML doc
     * @return  string  updated HTML doc with CDN links
     */

    public function rewrite($html)
    {
        // check if HTTPS and use CDN over HTTPS enabled
        if (!$this->https && isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on') {
            return $html;
        }

        // get dir scope in regex format
        $dirs = $this->get_dir_scope();
        $blog_url = $this->https
            ? '(https?:|)'.$this->relative_url(preg_quote($this->blog_url))
            : '(http:|)'.$this->relative_url(preg_quote($this->blog_url));

        // regex rule start
        $regex_rule = '#(?<=[(\"\'])';

        // check if relative paths
        if ($this->relative) {
            $regex_rule .= '(?:'.$blog_url.')?';
        } else {
            $regex_rule .= $blog_url;
        }

        // regex rule end
        $regex_rule .= '/(?:((?:'.$dirs.')[^\"\')]+)|([^/\"\']+\.[^/\"\')]+))(?=[\"\')])#';

        // call the cdn rewriter callback
        $cdn_html = preg_replace_callback($regex_rule, [$this, 'rewrite_url'], $html);

        return $cdn_html;
    }
}
