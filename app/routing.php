<?php
//***************************************
// Montage des controleurs sur le routeur
$app->mount("/", new App\Controller\IndexController($app));
$app->mount("/produit", new App\Controller\AquariumController($app));
$app->mount("/connexion", new App\Controller\UserController($app));
$app->mount("/panier", new App\Controller\PanierController($app));
$app->before(function (\Symfony\Component\HttpFoundation\Request $request) use ($app) {
    $nomRoute=$request->get("_route"); //var_dump($request) pour voir
    if ($app['session']->get('droit') != 'DROITadmin'  && $nomRoute=="index.pageAdmin") {
        return $app->redirect($app["url_generator"]->generate("index.errorDroit"));
    }
});
