<?php

namespace SpaceSpell\ElasticApmBundle\Support;

use Symfony\Component\HttpFoundation\Request;

class RequestConverter
{
    public static function getTransactionName(Request $request)
    {
        // $method = $request->getMethod();
        // $routeName = $request->get('_route');
        $controllerName = $request->get('_controller');

        return sprintf('%s', $controllerName);
    }
}
