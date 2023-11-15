<?php

namespace Anomaly\SitemapExtension\Http\Controller;

use Anomaly\Streams\Platform\Support\Resolver;
use Anomaly\SitemapExtension\Event\BuildSitemap;
use Anomaly\SitemapExtension\Event\GatherSitemaps;
use Anomaly\Streams\Platform\Addon\AddonCollection;
use Anomaly\Streams\Platform\Addon\Command\GetAddon;
use Anomaly\Streams\Platform\Http\Controller\PublicController;
use Carbon\Carbon;
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Sitemap;
use Spatie\Sitemap\Tags\Url;

/**
 * Class SitemapController
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 */
class SitemapController extends PublicController
{

    /**
     * Return an index of sitemaps.
     *
     * @param AddonCollection $addons
     * @return string
     */
    public function index(AddonCollection $addons)
    {
        $sitemapIndex = SitemapIndex::create();

        /* @var Addon $addon */
        foreach ($addons->withConfig('sitemap')->forget(['anomaly.extension.sitemap']) as $addon) {

            /* @var Module|Extension $addon */
            if (in_array($addon->getType(), ['module', 'extension']) && !$addon->isEnabled()) {
                continue;
            }

            /**
             * Loop over the various
             * sitemaps for this addon.
             */
            foreach (config($addon->getNamespace('sitemap')) as $file => $configuration) {

                if (is_string($configuration)) {
                    $configuration = [
                        'repository' => $configuration,
                    ];
                }

                if (is_array($configuration) && isset($configuration['repository'])) {

                    $repository = array_get($configuration, 'repository');

                    if (is_string($repository) && (class_exists($repository) || interface_exists($repository))) {

                        /* @var EntryRepositoryInterface|Hookable $repository */
                        $repository = $this->container->make($repository);
                    }

                    if (is_callable($repository)) {

                        /* @var EntryRepositoryInterface|Hookable $repository */
                        $repository = app(Resolver::class)->resolve($repository);
                    }

                    if ($lastModifiedEntry = $repository->lastModified()) {
                        $sitemapIndex->add(
                            Sitemap::create($this->url->to('sitemap/' . $addon->getNamespace() . '/' . $file . '.xml'))
                            ->setLastModificationDate($lastModifiedEntry->lastModified())
                        );
                    }

                    continue;
                }

                if (is_array($configuration) && isset($configuration['lastmod']) && is_callable($configuration['lastmod'])) {

                    $lastmod = new Carbon(app(Resolver::class)->resolve($configuration['lastmod']));

                    $sitemapIndex->add(
                        Sitemap::create($this->url->to('sitemap/' . $addon->getNamespace() . '/' . $file . '.xml'))
                            ->setLastModificationDate($lastmod)
                    );
                }
            }
        }

        event(new GatherSitemaps($sitemapIndex));

        return $this->response->make(
            $sitemapIndex->render(),
            200,
            [
                'Content-Type' => 'application/xml',
            ]
        );
    }

    /**
     * Return a sitemap.
     *
     * @param $addon
     * @param $file
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function view($addon, $file)
    {
        $sitemap = \Spatie\Sitemap\Sitemap::create();

        $addon = dispatch_sync(new GetAddon($addon));

        $configuration = config($hint = $addon->getNamespace('sitemap.' . $file));

        if (is_string($configuration)) {
            $configuration = [
                'repository' => $configuration,
            ];
        }

        $repository = array_get($configuration, 'repository');

        if (is_string($repository) && (class_exists($repository) || interface_exists($repository))) {

            /* @var EntryRepositoryInterface|Hookable $repository */
            $repository = $this->container->make($repository);
        }

        if (is_callable($repository)) {

            /* @var EntryRepositoryInterface|Hookable $repository */
            $repository = app(Resolver::class)->resolve($repository);
        }

        // Cache TTL (1hr)
        $ttl = array_get($configuration, 'ttl', 60 * 60);

        /**
         * Cache everything using the repository.
         *
         * @var Sitemap
         */
        $sitemap = $repository->cache(
            md5($addon . $file),
            $ttl,
            function () use ($sitemap, $repository, $configuration) {

                /* @var EntryInterface $model */
                $model = $repository->getModel();

                /* @var StreamInterface $stream */
                $stream = $model->getStream();

                $translatable = $stream->isTranslatable();

                $locales = config('streams::locales.enabled');
                $default = config('streams::locales.default');

                $priority  = array_get($configuration, 'priority', 0.5);
                $frequency = array_get($configuration, 'frequency', 'weekly');
                $route     = array_get($configuration, 'route', 'view');

                /* @var EntryInterface $entry */
                foreach ($repository->call('get_sitemap') as $entry) {
                    $lastmod = $entry->lastModified();

                    $sitemap->add(
                        Url::create(url($entry->route($route) ?: '/'))
                            ->setLastModificationDate($lastmod)
                            ->setPriority($priority)
                            ->setChangeFrequency($frequency)
                    );

                    if ($translatable) {
                        foreach ($locales as $locale) {
                            if ($locale != $default) {
                                config(['app.locale' => $locale]);

                                $uri = $locale;

                                $path = $entry->route($route) ?: '/';

                                if($path !== '/') {
                                    $uri .= $path;
                                }

                                $sitemap->add(
                                    Url::create(url($uri))
                                        ->setLastModificationDate($lastmod)
                                        ->setPriority($priority)
                                        ->setChangeFrequency($frequency)
                                );
                            }
                        }

                        //reset to default locale
                        config(['app.locale' => $default]);
                    }
                }

                event(new BuildSitemap($sitemap));

                return $sitemap->render();
            }
        );

        return $this->response->make(
            $sitemap,
            200,
            [
                'Content-Type' => 'application/xml',
            ]
        );
    }
}
