<?php

return [
    'cache' => config('app.debug') ? null : env('SITEMAP_CACHE', 3600)
];
