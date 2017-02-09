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
use App\Forms\ContactFormFactory;

class HomepagePresenter extends BasePresenter {

	/** @var VehiclesFilterFormFactory @inject */
	public $vehiclesFilterFormFactory;
	
	/** @var RentalOrderFormFactory @inject */
	public $rentalOrderFormFactory;	

	/** @var ContactFormFactory @inject */
	public $contactFormFactory;		

	public function renderVehiclesList() {
		if(empty($_SESSION['filters'])) {
			$vehicles = $this->vehicle->findAll()
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
		}
		
	    $texts = $this->text->findAll();
	    $template_texts = [];

	    foreach ($texts as $text) {
	    	$template_texts[$text->title] = $text->text;
	    }

	    $this->template->text = $template_texts;
		$this->template->vehicles = $vehicles;
		$this->template->paginator_pages = ceil($vehicles->count()/3);
		$this->redrawControl('main');
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
			$latte = new Latte\Engine;		
			$params = array(
				'surname' => $values['data']['surname'],
				'email' => $values['data']['email'],
			);
			
			$template = $latte->renderToString('../app/templates/components/reservation-confirm-email.latte', $params);
	        
	        $mail = new Message;
			$mail->setFrom("Allrisk <careffective@allrisk.cz>")
	        	 ->addTo($values['data']['surname']." <".$values['data']['email'].">")
	             ->setSubject("Potvrzení rezervace")
				 ->setHtmlBody($template);
	             
	        $mailer = new SendmailMailer;
	        $mailer->send($mail);
		    
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

	protected function createComponentContactForm() {
		$form = $this->contactFormFactory->create();

		$form->onSuccess[] = function ($form, $values) {			
			$latte = new Latte\Engine;		
			$params = array(
				'name' => $values['name'],
				'phone' => $values['phone'],
				'email' => $values['email'],
				'message' => $values['message'],
			);
			
			$template = $latte->renderToString('../app/templates/components/contact-email.latte', $params);
	        
	        $mail = new Message;
			$mail->setFrom("Allrisk <careffective@allrisk.cz>")
	        	 ->addTo($values['name']." <".$values['email'].">")
	             ->setSubject("Zpráva z webu careffective")
				 ->setHtmlBody($template);
	             
	        $mailer = new SendmailMailer;
	        $mailer->send($mail);

	        $this->flashMessage('Váše zpráva byla odeslána. Děkujeme.', 'success');
			$this->redirect('vehiclesList');
		};
		
		return $form;
    }
}
