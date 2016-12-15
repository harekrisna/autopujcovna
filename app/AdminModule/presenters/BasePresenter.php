<?php

namespace App\AdminModule\Presenters;

use Nette;
use App\Model;
use Tracy\Debugger;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter {
	
	/** @var Car */
	protected $car;

	/** @var User */
	protected $user;
	
	protected function startup() {
		parent::startup();
		
        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:in');
        }		
        
        $this->car = $this->context->getService('car');
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

	public function beforeRender() {
		$this->template->this_week = date("W");
        $this->template->this_year = date("Y");
	}
}