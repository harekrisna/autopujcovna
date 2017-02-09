<?php

namespace App\Presenters;

use Nette;
use App\Model;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter {
	/** @var Vehicle */
	protected $vehicle;	
	/** @var Text */
	protected $text;	
	
	protected function startup() {
		parent::startup();
        $this->vehicle = $this->context->getService('vehicle');
        $this->text = $this->context->getService('text');

   		\RadekDostal\NetteComponents\DateTimePicker\DateTimePicker::register();
	}

}
