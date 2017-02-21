<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Utils\DateTime;
use Latte;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use Tracy\Debugger;

class RentalOrderFormFactory extends Nette\Object {
	/** @var FormFactory */
	private $factory;
	/** @var Vehicle */
	private $vehicle;
	/** @var Brand */
	private $brand;
	/** @var RentalOrder */
	private $rental_order;

	private $record;
		
	public function __construct(FormFactory $factory, \App\Model\RentalOrder $rental_order, \App\Model\Vehicle $vehicle) {
		$this->factory = $factory;
		$this->rental_order = $rental_order;
		$this->vehicle = $vehicle;
	}

	public function create($record = null) {
		$this->record = $record;

		$form = $this->factory->create();
		$data = $form->addContainer('data');
		
		$data->addHidden('vehicle_id');
		
		$data->addText('name', 'Jméno', 512);
		$data->addText('surname', 'Příjmení', 512)
		 	 ->setRequired('Zadejte jméno prosím.');
		 	 
		$data->addText('email', 'Email', 30, 255)
			 ->setType('email')
			 ->setRequired('Zadejte email prosím.')
			 ->addCondition($form::FILLED)
			  	 ->addRule(\Nette\Forms\Form::EMAIL, 'Zadejte platnou emailovou adresu');

		$data->addText('phone', 'Telefon', 32)
			 ->setRequired('Zadejte telefon prosím.');
			 
		$data->addText('give_place', 'Místo přistavení', 512);
		$data->addDateTimePicker('give_time', 'Čas přistavení')
			 ->setRequired('Zadejte čas přistavení prosím.');
			 
		$data->addText('take_place', 'Místo odstavení', 512);
		$data->addDateTimePicker('take_time', 'Čas odstavení')
			 ->setRequired('Zadejte čas odstavení prosím.');
			 
		$data->addTextArea("note", "Poznámka");

	    $form->addSubmit('add', 'Rezervovat');

		$form->onSuccess[] = array($this, 'formSucceeded');
		return $form;
	}

	public function formSucceeded(Form $form, $values) {
		if($this->rental_order->insert($values->data)) {
			$latte = new Latte\Engine;		
			$params = array(
				'data' => $values['data'],
				'vehicle' => $this->vehicle->get($values['data']['vehicle_id']),
			);
			
			$template = $latte->renderToString('../app/templates/components/reservation-confirm-email.latte', $params);
	        
	        $mail = new Message;
			$mail->setFrom("autopujcovna@allrisk.cz <autopujcovna@allrisk.cz>")
	        	 ->addTo($values['data']['surname']." <".$values['data']['email'].">")
	        	 ->addTo("autopujcovna@allrisk.cz <autopujcovna@allrisk.cz>")
	             ->setSubject("Potvrzení rezervace vozidla")
				 ->setHtmlBody($template);
	        
	        $mailer = new SendmailMailer;
	        $mailer->send($mail);
	        $phone = str_replace(' ', '', $values['data']['phone']);
	        mail("<sms:".$phone."@allrisk.cz>", '', "Rezervace byla přijata, budeme Vás kontaktovat. Děkujeme.");
        }
	}
}
