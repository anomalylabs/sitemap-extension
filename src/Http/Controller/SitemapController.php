<?php namespace Anomaly\SitemapExtension\Http\Controller;

use Anomaly\Streams\Platform\Addon\Addon;
use Anomaly\Streams\Platform\Addon\AddonCollection;
use Anomaly\Streams\Platform\Http\Controller\PublicController;
use Anomaly\Streams\Platform\Routing\UrlGenerator;
use Illuminate\Contracts\Config\Repository;
use Roumen\Sitemap\Sitemap;

/**
 * Class SitemapController
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 * @package       Anomaly\SitemapExtension\Http\Controller
 */
class SitemapController extends PublicController
{

    /**
     * The URL generator.
     *
     * @var UrlGenerator
     */
    private $url;

    /**
     * The config repository.
     *
     * @var Repository
     */
    protected $config;

    /**
     * The addon collection.
     *
     * @var AddonCollection
     */
    protected $addons;

    /**
     * The sitemap utility.
     *
     * @var Sitemap
     */
    protected $sitemap;

    /**
     * Create a new SitemapController instance.
     *
     * @param UrlGenerator    $url
     * @param Repository      $config
     * @param AddonCollection $addons
     * @param Sitemap         $sitemap
     */
    public function __construct(UrlGenerator $url, Repository $config, AddonCollection $addons, Sitemap $sitemap)
    {
        parent::__construct();

        $this->url     = $url;
        $this->config  = $config;
        $this->addons  = $addons;
        $this->sitemap = $sitemap;
    }

    /**
     * Return an index of sitemaps.
     *
     * @param null $format
     * @return \Illuminate\Support\Facades\View
     */
    public function index($format = null)
    {
        /* @var Addon $addon */
        foreach ($this->addons->withConfig('sitemap')->forget(['anomaly.extension.sitemap']) as $addon) {

            $lastmod = $this->config->get($addon->getNamespace('sitemap.lastmod'));

            $this->sitemap->addSitemap(
                $this->config->get(
                    $addon->getNamespace('sitemap.location') . $format,
                    $this->url->to($addon->getSlug() . '/sitemap' . $format)
                ),
                $lastmod ? $this->container->call($lastmod) : null
            );
        }

        return $this->sitemap->render('sitemapindex');
    }

    /**
     * Return a sitemap.
     *
     * @param null $format
     * @return \Illuminate\Support\Facades\View
     */
    public function view($format = null)
    {
        $addon = $this->addons->get(array_get($this->route->getAction(), 'addon'));

        foreach ($this->container->call($this->config->get($addon->getNamespace('sitemap.entries'))) as $entry) {

            if ($handler = $addon->getNamespace('sitemap.handler')) {
                $this->container->call(
                    $this->config->get($handler),
                    [
                        'entry'   => $entry,
                        'sitemap' => $this->sitemap
                    ]
                );
            } elseif ($parameters = $addon->getNamespace('sitemap.parameters')) {
                $this->container->call(
                    [
                        $this->sitemap,
                        'add'
                    ],
                    $this->container->call($parameters, compact('entry'))
                );
            }
        }

        return $this->sitemap->render(ltrim($format, '.'));
    }
}
