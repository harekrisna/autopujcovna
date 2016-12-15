<?php

namespace App\AdminModule\Model;
use Nette;
use Tracy\Debugger;

/**
 * Model starající se o tabulku centre
 */
class Car extends TableExtended {
    /** @var string */
	protected $tableName = 'car';
}