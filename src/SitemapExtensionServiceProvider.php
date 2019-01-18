<?php namespace Anomaly\SitemapExtension;

use Anomaly\Streams\Platform\Addon\AddonServiceProvider;
use Laravelium\Sitemap\SitemapServiceProvider;

/**
 * Class SitemapExtensionServiceProvider
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class SitemapExtensionServiceProvider extends AddonServiceProvider
{

    /**
     * The addon providers.
     *
     * @var array
     */
    protected $providers = [
        SitemapServiceProvider::class,
    ];

    /**
     * The addon routes.
     *
     * @var array
     */
    protected $routes = [
        'sitemap.xml'                => 'Anomaly\SitemapExtension\Http\Controller\SitemapController@index',
        'sitemap/{addon}/{file}.xml' => 'Anomaly\SitemapExtension\Http\Controller\SitemapController@view',
    ];

}
