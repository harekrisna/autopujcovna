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
	/** @var Photo */
	protected $photo;
	/** @var User */
	protected $user;
	
	protected function startup() {
		parent::startup();
		
        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:in');
        }		
        
        $this->vehicle = $this->context->getService('vehicle');
        $this->brand = $this->context->getService('brand');
        $this->photo = $this->context->getService('photo');
		$this->user = $this->getUser();
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
}