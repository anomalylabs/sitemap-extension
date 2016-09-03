<?php namespace Anomaly\SitemapExtension;

use Anomaly\Streams\Platform\Addon\Addon;
use Anomaly\Streams\Platform\Addon\AddonCollection;
use Anomaly\Streams\Platform\Addon\AddonServiceProvider;
use Anomaly\Streams\Platform\Addon\Extension\Extension;
use Anomaly\Streams\Platform\Addon\Module\Module;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Routing\Router;

class SitemapExtensionServiceProvider extends AddonServiceProvider
{

    /**
     * The addon providers.
     *
     * @var array
     */
    protected $providers = [
        'Roumen\Sitemap\SitemapServiceProvider',
    ];

    /**
     * The addon routes.
     *
     * @var array
     */
    protected $routes = [
<<<<<<< HEAD
        'sitemap{format}' => 'Anomaly\SitemapExtension\Http\Controller\SitemapController@index',
=======
        'sitemap{format?}' => 'Anomaly\SitemapExtension\Http\Controller\SitemapController@index',
>>>>>>> 2.1
    ];

    /**
     * Map additional routes.
     *
     * @param Repository      $config
     * @param Router          $router
     * @param AddonCollection $addons
     */
    public function map(Repository $config, Router $router, AddonCollection $addons)
    {
        /* @var Addon $addon */
        foreach ($addons->withConfig('sitemap')->forget(['anomaly.extension.sitemap']) as $addon) {

            /* @var Module|Extension $addon */
            if (in_array($addon->getType(), ['module', 'extension']) && !$addon->isEnabled()) {
                continue;
            }

            $router->get(
                $config->get(
                    $addon->getNamespace('sitemap.location') . '{format?}',
                    $addon->getSlug() . '/sitemap{format?}'
                ),
                [
                    'addon' => $addon->getNamespace(),
                    'uses'  => 'Anomaly\SitemapExtension\Http\Controller\SitemapController@view',
                ]
            );
        }
    }

    /**
     * Boot the service provider.
     *
     * @param Repository $config
     */
    public function boot(Repository $config)
    {
        $config->set('sitemap', $config->get('anomaly.extension.sitemap::sitemap'));
    }
}
