<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Utils\DateTime;
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
		
	public function __construct(FormFactory $factory, \App\Model\RentalOrder $rental_order) {
		$this->factory = $factory;
		$this->rental_order = $rental_order;
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
			 
		$data->addText("note", "Poznámka");

	    $form->addSubmit('add', 'Rezervovat');

		$form->onSuccess[] = array($this, 'formSucceeded');
		return $form;
	}

	public function formSucceeded(Form $form, $values) {
		$this->rental_order->insert($values->data);
	}
}
