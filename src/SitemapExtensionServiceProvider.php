<?php namespace Anomaly\SitemapExtension;

use Anomaly\Streams\Platform\Addon\AddonServiceProvider;

/**
 * Class SitemapExtensionServiceProvider
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 * @package       Anomaly\SitemapExtension
 */
class SitemapExtensionServiceProvider extends AddonServiceProvider
{

    /**
     * The addon routes.
     *
     * @var array
     */
    protected $routes = [
        //'sitemap.xml' => ''
    ];

    /**
     * The addon providers.
     *
     * @var array
     */
    protected $providers = [];

    /**
     * The addon singletons.
     *
     * @var array
     */
    protected $singletons = [];

}
