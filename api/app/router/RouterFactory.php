<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App;

use Nette\Application\IRouter;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;

/**
 * Router factory.
 */
class RouterFactory
{
    /**
     * @return IRouter
     */
    public function createRouter()
    {

        $router = new RouteList();
        
        /*$router[] = $apiRouter = new RouteList('Api');
        $apiRouter[] = new Route('security/<action>', [
            'presenter' => 'Security',
            'action' => 'default'
            ]
        );
        $apiRouter[] = new Route('v1/map/<query>', [
                'presenter' => 'V1',
                'action' => 'map'
            ]
        );
        $apiRouter[] = new Route('v1/categories/<languageCode>', [
                'presenter' => 'V1',
                'action' => 'categories'
            ]
        );
        $apiRouter[] = new Route('v1/countries', [
                'presenter' => 'V1',
                'action' => 'countries'
            ]
        );
        $apiRouter[] = new Route('v1/uploader', [
                'presenter' => 'V1',
                'action' => 'uploader'
            ]
        );
        $apiRouter[] = new Route('v1/delete-file/<id>', [
                'presenter' => 'V1',
                'action' => 'deleteFile'
            ]
        );
        $apiRouter[] = new Route('<presenter>[/<entity>][/<id>][/<locale>][/<fields>][/<condition>][/<order>][/<limit>][/<offset>]', [
            'presenter' => 'V1',
            'action' => 'default'
            ]
        );*/

        return $router;
    }
}
