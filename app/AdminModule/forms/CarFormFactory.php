<?php

namespace App\AdminModule\Forms;

use Nette;
use Nette\Application\UI\Form;
use Tracy\Debugger;

class CarFormFactory extends Nette\Object {
	/** @var FormFactory */
	private $factory;
	/** @var Car */
	private $car;

	private $record;
		
	public function __construct(FormFactory $factory, \App\Model\Car $car) {
		$this->factory = $factory;
		$this->car = $car;
	}

	public function create($record = null) {
		$this->record = $record;

		$form = $this->factory->create();
		$data = $form->addContainer('data');

		$data->addText('rz', 'RZ')
			 ->setRequired('Zadejte RZ automobilu prosím.');

	    $form->addSubmit('add', 'Přidat automobil');
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
				$this->car->insert($values->data);
			}
			else {
				$this->car->update($this->record->id, $values->data);
			}
		}
		catch(\App\Model\DuplicateException $e) {
			if($e->foreign_key == "rz") {
				$form['data']['rz']->addError("Automobil s tímto RZ již existuje.");
			}
		}
	}
}
