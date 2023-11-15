<?php

namespace Anomaly\SitemapExtension\Event;

use Spatie\Sitemap\SitemapIndex;

/**
 * Class GatherSitemaps
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 */
class GatherSitemaps
{

    /**
     * The sitemap instance.
     *
     * @var SitemapIndex
     */
    protected $sitemap;

    /**
     * Create a new class instance.
     *
     * @param SitemapIndex $sitemap
     */
    public function __construct(SitemapIndex $sitemap)
    {
        $this->sitemap = $sitemap;
    }

    /**
     * Get the sitemap.
     * 
     * @return SitemapIndex
     */
    public function getSitemap()
    {
        return $this->sitemap;
    }
}
