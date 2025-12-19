# robots.txt for SV Masala & Herbal Products
# https://www.robotstxt.org/robotstxt.html

User-agent: *
Allow: /

# Sitemap location
Sitemap: {{ url('/sitemap.xml') }}

# Disallow admin and private areas
Disallow: /admin/
Disallow: /admin/*
Disallow: /api/
Disallow: /storage/
Disallow: /_debugbar/

# Disallow search result pages with parameters (to avoid duplicate content)
Disallow: /*?sort=
Disallow: /*?page=
Disallow: /*?q=

# Allow important CSS and JS for rendering
Allow: /build/
Allow: /*.css$
Allow: /*.js$

# Crawl delay (optional - be nice to servers)
Crawl-delay: 1

# Specific bot rules
User-agent: Googlebot
Allow: /
Crawl-delay: 0

User-agent: Bingbot
Allow: /
Crawl-delay: 1

User-agent: Googlebot-Image
Allow: /storage/products/
Allow: /images/
