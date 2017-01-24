<?php

namespace App\Presenters;

use Nette;
use App\Model;
use Tracy\Debugger;
use Latte;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use App\Forms\VehiclesFilterFormFactory;
use App\Forms\RentalOrderFormFactory;

class HomepagePresenter extends BasePresenter {

	/** @var VehiclesFilterFormFactory @inject */
	public $vehiclesFilterFormFactory;
	
	/** @var RentalOrderFormFactory @inject */
	public $rentalOrderFormFactory;	

	public function renderVehiclesList() {
		if(empty($_SESSION['filters'])) {
			$this->template->vehicles = $this->vehicle->findAll()
													  ->order('price DESC');
		}
		else {
			$vehicles = $this->vehicle->findBy(['brand_id' => $_SESSION['filters']['brand_id'],
										 		'bodywork' => $_SESSION['filters']['bodywork'],
										 		'fuel' => $_SESSION['filters']['fuel'],
										 		'transmission' => $_SESSION['filters']['transmission']]);

			if(empty($_SESSION['order_by'])) {
				$vehicles = $vehicles->order('brand_id, type');
			}
			else {
				$vehicles = $vehicles->order($_SESSION['order_by']);	
			}

			$this->template->vehicles = $vehicles;
			$this->redrawControl('main');
		}
	}
	
	public function renderVehicleDetail($vehicle_id) {
		$this->template->vehicle = $this->vehicle->get($vehicle_id);
		$this['rentalOrderForm']['data']['vehicle_id']->setValue($vehicle_id);
		$this['rentalOrderForm']->setDefaults([]);
		$this->redrawControl('main');
	}

	public function handleSetOrderBy($order_by) {
		$order_by == "price" ? $_SESSION['order_by'] = "price DESC" : "";
		$order_by == "title" ? $_SESSION['order_by'] = "brand_id.title ASC, type ASC" : "";
		$this->setView("vehiclesList");
	}

	public function actionSendMail() {
		/*
		$latte = new Latte\Engine;		
		$params = array(
			'surname' => "TEST",
			'email' => "TEST@TEST",
		);
		
		$template = $latte->renderToString('../app/templates/components/reservation-confirm-email.latte', $params);
        
        $mail = new Message;
		$mail->setFrom("Allrisk <careffective@allrisk.cz>")
        	 ->addTo("TEST"." <".RESERVATION_CONFIRM_EMAIL.">")
             ->setSubject("Potvrzení rezervace")
			 ->setHtmlBody($template);
             
        $mailer = new SendmailMailer;
        $mailer->send($mail);
        */

        ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);

        $to      = 'barta.michal@allrisk.cz';
		$subject = 'the subject';
		$message = 'hello';
		$headers = 'From: careffective@allrisk.cz' . "\r\n" .
    			   'Reply-To: careffective@allrisk.cz' . "\r\n" .
    		       'X-Mailer: PHP/' . phpversion();

		mail($to, $subject, $message, $headers);

		$this->sendResponse(new Nette\Application\Responses\TextResponse('$to      = \'barta.michal@allrisk.cz\';<br/>
		$subject = \'the subject\';<br/>
		$message = \'hello\';<br/>
		$headers = \'From: careffective@allrisk.cz\'<br/>
    	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\'Reply-To: careffective@allrisk.cz\'<br/>
    	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\'X-Mailer: PHP/\' . phpversion();<br/>
<br/>
		mail($to, $subject, $message, $headers);'));
	}

	protected function createComponentVehiclesFilterForm() {
		$form = $this->vehiclesFilterFormFactory->create();

		$form->onSuccess[] = function ($form, $values) {			
			$_SESSION['filters']['brand_id'] = $values->brands;
			$_SESSION['filters']['bodywork'] = $values->bodywork;
			$_SESSION['filters']['fuel'] = $values->fuel;
			$_SESSION['filters']['transmission'] = $values->transmission;													  	
			$this->setView("vehiclesList");
		};
		
		return $form;
	}
	
	protected function createComponentRentalOrderForm() {
		$form = $this->rentalOrderFormFactory->create();

		$form->onSuccess[] = function ($form, $values) {
			try {
				$latte = new Latte\Engine;		
				$params = array(
					'surname' => $values['data']['surname'],
					'email' => $values['data']['email'],
				);
				
				$template = $latte->renderToString('../app/templates/components/reservation-confirm-email.latte', $params);
		        
		        $mail = new Message;
				$mail->setFrom("Allrisk <careffective@allrisk.cz>")
		        	 ->addTo($values['data']['surname']." <".RESERVATION_CONFIRM_EMAIL.">")
		             ->setSubject("Potvrzení rezervace")
					 ->setHtmlBody($template);
		             
		        $mailer = new SendmailMailer;
		        $mailer->send($mail);
		    } catch(Nette\Mail\SendException $e) {}

			$this->flashMessage("Rezervace byla přijata", 'success');
			
			if(!$this->isAjax()) {
				$this->redirect($this->getAction(), $this->getParameter('vehicle_id'));
			}
			else {
				$form->setValues([], true);
				$this->redrawControl('flashes');
			}
		};
		
		return $form;
	}	
}
