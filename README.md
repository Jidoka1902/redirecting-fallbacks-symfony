# redirecting-fallbacks-symfony
Symfony Integration for the "redirecting-fallbacks" project.

# Examples
It's all about the configuration.
## Basic Configuration
First of all, load the integration and library classes as services.
So you can wire them as you need.
```yaml
## config/services.yaml
services:
  Jidoka1902\RedirectingFallbacks\:
    resource: '%kernel.project_dir%/vendor/jidoka1902/redirecting-fallbacks/src'
  Jidoka1902\RedirectingFallbacksSymfony\:
    resource: '%kernel.project_dir%/vendor/jidoka1902/redirecting-fallbacks-symfony/src'

```
After that you can decide what type of routes you want to specify in your configuration files.
Either plain paths like "/" and "/blog" or symfony named routes like "app_index".

For Symfony Routing - use the provided __FrameworkUrlGeneratorAdapter__:
```yaml
## config/services.yaml
services:
  Jidoka1902\RedirectingFallbacks\UrlGenerator\UrlGenerator:
    alias: Jidoka1902\RedirectingFallbacksSymfony\UrlGenerator\FrameworkUrlGeneratorAdapter
```

If you prefer plain paths to redirect to use this config:
```yaml
## config/services.yaml
services:
  Jidoka1902\RedirectingFallbacks\UrlGenerator\UrlGenerator:
    alias: Jidoka1902\RedirectingFallbacks\UrlGenerator\PassthroughUrlGenerator
```
## Gloal Redirect on 404
Now that you know how to configure your prefered path-type - let's have a basic example
about one redirect path for all occuring 404 Status Reasons (NotFoundHttpException or plain ResponseCode 404).
- you have to wire the __SingleRedirectResolver__ as alias for the RedirectResolver interface.
```yaml
## config/services.yaml
services:
  Jidoka1902\RedirectingFallbacks\Resolver\RedirectResolver:
    alias: Jidoka1902\RedirectingFallbacks\Resolver\SingleRedirectResolver
```
- configure the route to redirect to
```yaml
## config/services.yaml
services:
  Jidoka1902\RedirectingFallbacks\Resolver\SingleRedirectResolver:
    arguments:
      $target: "/"
```
è voila! every request resulting in a 404 state will be redirected to "/"
## More Precise Redirects depending on Paths
After that basic configuration, lets have a look at how to create more individual 404-redirects.
E.g. if someone enters an not found blog url because of a wrong slug which was not found in your storage,
should that be redirected to the start-page or do you have a fancy blog post search site?
Same here:
- link your desired RedirectResolver:
```yaml
## config/services.yaml
services:
  Jidoka1902\RedirectingFallbacks\Resolver\RedirectResolver:
    alias: Jidoka1902\RedirectingFallbacks\Resolver\MultipleRedirectResolver
```
- configure it:
```yaml
## config/services.yaml
services:
  Jidoka1902\RedirectingFallbacks\Resolver\MultipleRedirectResolver:
    arguments:
      $mapping:
        - { path: '/blog/', target: 'app_blog_search'}
        - { path: '/', target: 'app_index'} 
```
But beware! the order of your mappings plays a role. 