<?php namespace Anomaly\SitemapExtension;

use Anomaly\Streams\Platform\Addon\Addon;
use Anomaly\Streams\Platform\Addon\AddonCollection;
use Anomaly\Streams\Platform\Addon\AddonServiceProvider;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Routing\Router;

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
     * The addon providers.
     *
     * @var array
     */
    protected $providers = [
        'Roumen\Sitemap\SitemapServiceProvider'
    ];

    /**
     * The addon routes.
     *
     * @var array
     */
    protected $routes = [
        'sitemap{format?}' => 'Anomaly\SitemapExtension\Http\Controller\SitemapController@index'
    ];

    /**
     * Boot the addon.
     *
     * @param Repository      $config
     * @param Router          $router
     * @param AddonCollection $addons
     */
    public function boot(Repository $config, Router $router, AddonCollection $addons)
    {
        /* @var Addon $addon */
        foreach ($addons->withConfig('sitemap')->forget(['anomaly.extension.sitemap']) as $addon) {
            $router->get(
                $config->get(
                    $addon->getNamespace('sitemap.location') . '{format?}',
                    $addon->getSlug() . '/sitemap{format?}'
                ),
                [
                    'addon' => $addon->getNamespace(),
                    'uses'  => 'Anomaly\SitemapExtension\Http\Controller\SitemapController@view'
                ]
            );
        }
    }

}
