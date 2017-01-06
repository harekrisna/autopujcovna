<?php

namespace App\AdminModule\Forms;

use Nette;
use Nette\Forms;
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
		
	public function __construct(FormFactory $factory, \App\Model\Vehicle $vehicle, \App\Model\Brand $brand, \App\Model\RentalOrder $rental_order) {
		$this->factory = $factory;
		$this->vehicle = $vehicle;
		$this->brand = $brand;
		$this->rental_order = $rental_order;
	}

	public function create($record = null) {
		$this->record = $record;

		$form = $this->factory->create();
		$data = $form->addContainer('data');
		
		$vehicles = $this->vehicle->findAll()->fetchPairs('id', 'rz');
		$data->addSelect("vehicle_id", "Vozidlo", $vehicles)
			 ->setPrompt("--- vyberte vozidlo ---");
		
		$data->addText('name', 'Jméno', 512);
		$data->addText('surname', 'Příjmení', 512)
		 	 ->setRequired('Zadejte jméno prosím.');
		 	 
		$data->addText('email', 'Email:', 30, 255)
			 ->setType('email')
			 ->setRequired('Zadejte email prosím.')
			 ->addCondition($form::FILLED)
			  	 ->addRule(\Nette\Forms\Form::EMAIL, 'Zadejte platnou emailovou adresu');

		$data->addText('phone', 'Telefon', 32)
			 ->setRequired('Zadejte telefon prosím.');
		$data->addText('give_place', 'Místo přistavení', 512);
		$data->addDateTimePicker('give_time', 'Čas přistavení', 10, 10);
		$data->addText('take_place', 'Místo odstavení', 512);
		$data->addDateTimePicker('take_time', 'Čas odstavení', 10, 10);
		$data->addText("note", "Poznámka");

	    $form->addSubmit('add', 'Vytvořit objednávku');
	    $form->addSubmit('edit', 'Uložit změny');

	    if($record != null) {
	    	$form['data']->setDefaults($record);
	    }

		$form->onSuccess[] = array($this, 'formSucceeded');
		return $form;
	}

	public function formSucceeded(Form $form, $values) {
		try {
			if($form->isSubmitted()->name == "add") {
				$this->vehicle->insert($values->data);
			}
			else {
				$this->vehicle->update($this->record->id, $values->data);
			}
		}
		catch(\App\Model\DuplicateException $e) {
		}
	}
}
