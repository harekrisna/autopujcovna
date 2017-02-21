<?php

namespace App\AdminModule\Presenters;

use Nette;
use App\Model;
use Tracy\Debugger;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter {	
	/** @var Vehicle */
	protected $vehicle;	
	/** @var Brand */
	protected $brand;
	/** @var RentalOrder */
	protected $rentalOrder;
	/** @var Photo */
	protected $photo;
	/** @var Slide */
	protected $slide;
	/** @var Text */
	protected $text;
	/** @var User */
	protected $user;
	
	protected function startup() {
		parent::startup();
		
        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:in');
        }		
        
        $this->vehicle = $this->context->getService('vehicle');
        $this->photo = $this->context->getService('photo');
        $this->brand = $this->context->getService('brand');
        $this->rentalOrder = $this->context->getService('rentalOrder');
        $this->slide = $this->context->getService('slide');
        $this->text = $this->context->getService('text');
		$this->user = $this->getUser();

		\RadekDostal\NetteComponents\DateTimePicker\DateTimePicker::register();
	}

	public function flashMessage($message, $type = 'info') {
		if ($this->isAjax()) {
			$this->payload->messages[] = ['message' => $message,
										  'type' => $type];
		}
		else {
			parent::flashMessage($message, $type);
		}
	}
	
	public function beforeRender() {
		parent::beforeRender();
		
		$this->template->vehicles_count = $this->vehicle->findAll()->count();
	}
}