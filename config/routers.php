<?php

use Controller\Admin\CustomerReviewController\CustomerReviewController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $router) {
    $router->add('ps_democqrshooksusage_toggle_is_allowed_for_review','demo-cqrs-hook-usage/{customerId}/toggle-is-allowed-for-review')
        ->controller([CustomerReviewController::class , 'toggleIsAllowedForReviewActions'])
        ->methods(['POST'])
        ->requirements(['customerId' => '\d+']);
};