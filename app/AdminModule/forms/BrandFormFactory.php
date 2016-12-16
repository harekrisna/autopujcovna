<?php

namespace App\AdminModule\Forms;

use Nette;
use Nette\Application\UI\Form;
use Tracy\Debugger;

class BrandFormFactory extends Nette\Object {
	/** @var FormFactory */
	private $factory;
	/** @var Brand */
	private $model;

	private $record;
		
	public function __construct(FormFactory $factory, \App\Model\Brand $brand) {
		$this->factory = $factory;
		$this->model = $brand;
	}

	public function create($record = null) {
		$this->record = $record;

		$form = $this->factory->create();
		$data = $form->addContainer('data');

		$data->addText('title', 'Značka')
			 ->setRequired('Zadejte značku prosím.');

	    $form->addSubmit('add', 'Přidat značku');
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
				$this->model->insert($values->data);
			}
			else {
				$this->model->update($this->record->id, $values->data);
			}
		}
		catch(\App\Model\DuplicateException $e) {
			if($e->foreign_key == "title") {
				$form['data']['title']->addError("Značka s tímto jménem již existuje.");
			}
		}
	}
}
