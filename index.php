<?php
echo "<pre>";
require_once __DIR__ . "/vendor/autoload.php";



$file = __DIR__ .'/var/cache/container.php';

if (file_exists($file)) {
    require_once $file;
    $container = new ProjectServiceContainer();
} else {
    $locator = new \Symfony\Component\Config\FileLocator(__DIR__);
    $container = new \Symfony\Component\DependencyInjection\ContainerBuilder();
    $containerLoader = new \Symfony\Component\DependencyInjection\Loader\YamlFileLoader($container, $locator);
    $containerLoader->load('services/content/config/services.yml');
    $containerLoader->load('services/target/config/services.yml');
    $containerLoader->load('config/services.yml');
    $container->compile();

    $dumper = new \Symfony\Component\DependencyInjection\Dumper\PhpDumper($container);
    file_put_contents($file, $dumper->dump());
}


$dispatcher = FastRoute\cachedDispatcher(function(FastRoute\RouteCollector $routeCollector) {
    $loader = new \AdServer\Engine\Components\YamlRoutesLoader($routeCollector);
    $loader->loadRoutes(__DIR__ . '/config/routes.yml');
    $loader->loadRoutes(__DIR__ . '/services/content/config/routes.yml', '/content');
    $loader->loadRoutes(__DIR__ . '/services/target/config/routes.yml', '/target');
}, [
    'cacheFile' => __DIR__ . '/var/cache/route.cache', /* required */
]);

$router = new \AdServer\Engine\Components\Router(
    $dispatcher,
    $container
);

\AdServer\Engine\Components\Engine::run(
    $router,
    $container
);
