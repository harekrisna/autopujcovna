<?php

namespace App;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;


class RouterFactory
{

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter()
	{
		$router = new RouteList;

        # AdminModule route
        $router[] = new Route('admin1896/<presenter>/<action>[/<id>]', array(
        	'module'    => 'Admin',
            'presenter' => 'Vehicle',
            'action'    => 'list'
        ));

		$router[] = new Route('vozidlo[/<vehicle_id>]', 'Homepage:vehicleDetail');
		$router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:vehiclesList');
		return $router;
	}

}
