# Sitemap Extension

*anomaly.extension.sitemap*

#### A dynamic sitemap generator extension.

The Sitemap Extension automatically generates XML sitemaps for your PyroCMS application with support for multiple addons and custom entries.

## Features

- Automatic sitemap generation
- Multi-addon support
- Customizable priorities
- Change frequency configuration
- SEO optimization
- Dynamic content indexing
- Automatic URL discovery

## Usage

### Accessing Sitemap

The sitemap is automatically available at `/sitemap.xml` once the extension is installed.

### Configuration

Navigate to **Settings > Extensions > Sitemap** in the control panel to configure:

- Enabled addons/modules
- URL priorities
- Change frequencies
- Additional URLs

### Programmatic Access

```php
use Anomaly\SitemapExtension\Sitemap\SitemapGenerator;

$generator = app(SitemapGenerator::class);

// Generate sitemap
$sitemap = $generator->generate();

// Add custom URLs
$generator->add('/custom-page', [
    'priority' => 0.8,
    'changefreq' => 'weekly'
]);
```

### In Twig

```twig
{# Link to sitemap #}
<link rel="sitemap" type="application/xml" href="/sitemap.xml">

{# Generate sitemap link #}
<a href="{{ url('sitemap.xml') }}">Sitemap</a>
```

### Extending Sitemap

```php
// In your service provider
protected function boot()
{
    $this->app['sitemap']->add('/my-custom-url', [
        'priority' => 0.7,
        'changefreq' => 'monthly',
        'lastmod' => now()
    ]);
}
```

## Requirements

- Streams Platform ^1.10
- PyroCMS 3.10+
- Spatie Laravel Sitemap ^7.3+

## License

The Sitemap Extension is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
