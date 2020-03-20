<?php

namespace ImageEngine;

class Rewriter
{
    /**
     * Origin URL
     */
    public $blog_url;
 
    /**
     * CDN URL
     */
    public $cdn_url;

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
     * Constructor
     */
    public function __construct(
        $blog_url,
        $cdn_url,
        $dirs,
        array $excludes,
        $relative,
        $https
    ) {
        $this->blog_url       = $blog_url;
        $this->cdn_url        = $cdn_url;
        $this->dirs           = $dirs;
        $this->excludes       = $excludes;
        $this->relative       = $relative;
        $this->https          = $https;
    }


    /**
     * Exclude assets that should not be rewritten
     * @param   string  $asset  current asset
     * @return  boolean  true if need to be excluded
     */
    protected function exclude_asset(&$asset)
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
    protected function rewrite_url(&$asset)
    {
        if ($this->exclude_asset($asset[0])) {
            return $asset[0];
        }

        // Don't rewrite if in preview mode
        if (is_admin_bar_showing()
                && array_key_exists('preview', $_GET)
                && $_GET['preview'] == 'true') {
            return $asset[0];
        }

        $blog_url = $this->relative_url($this->blog_url);
        $subst_urls = [ 'http:'.$blog_url ];

        // rewrite both http and https URLs if we ticked 'enable CDN for HTTPS connections'
        if ($this->https) {
            $subst_urls = [
                'http:'.$blog_url,
                'https:'.$blog_url,
            ];
        }

        // is it a protocol independent URL?
        if (strpos($asset[0], '//') === 0) {
            return str_replace($blog_url, $this->cdn_url, $asset[0]);
        }

        // check if not a relative path
        if (!$this->relative || strstr($asset[0], $blog_url)) {
            return str_replace($subst_urls, $this->cdn_url, $asset[0]);
        }

        // relative URL
        return $this->cdn_url . $asset[0];
    }

    /**
     * Get directory scope
     * @return  string  directory scope
     */

    protected function get_dir_scope()
    {
        $input = explode(',', $this->dirs);
        if ($this->dirs == '' || count($input) === 0) {
            $input = ['wp-content', 'wp-includes'];
        }

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
