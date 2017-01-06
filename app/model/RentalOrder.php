<?php

namespace App\Model;
use Nette;
use Tracy\Debugger;

class RentalOrder extends TableExtended {
    /** @var string */
	protected $tableName = 'rental_order';
}