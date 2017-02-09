<?php

namespace App\AdminModule\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Utils\DateTime;
use Tracy\Debugger;

class TextFormFactory extends Nette\Object {
	/** @var FormFactory */
	private $factory;
	/** @var Text */
	private $model;
	/** @var Brand */
	private $brand;
	/** @var RentalOrder */
	private $rental_order;

	private $record;
		
	public function __construct(FormFactory $factory, \App\Model\Text $text) {
		$this->factory = $factory;
		$this->model = $text;
	}

	public function create() {
		$form = $this->factory->create();
		
		$form->addTextArea('services_column_1_text', 'Sloupec 1');
		$form->addTextArea('services_column_2_text', 'Sloupec 2');
		$form->addTextArea('services_column_3_text', 'Sloupec 3');
		$form->addTextArea('services_column_4_text', 'Sloupec 4');

	    $form->addSubmit('save', 'Uložit změny');

	    $texts = $this->model->findAll();

	    $defaults = [];

	    foreach ($texts as $text) {
	    	$defaults[$text->title] = $text->text;	
	    }

	    $form->setDefaults($defaults);

		$form->onSuccess[] = array($this, 'formSucceeded');
		return $form;
	}

	public function formSucceeded(Form $form, $values) {
		foreach ($values as $title => $value) {
			$section = $this->model->findBy(['title' => $title])
								   ->update(['text' => $value]);
		}
	}
}
