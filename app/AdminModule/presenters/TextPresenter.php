<?php

namespace App\AdminModule\Presenters;

use Nette;
use App\Model;
use Tracy\Debugger;
use Nette\Application\UI\Form;
use App\AdminModule\Forms\TextFormFactory;

class TextPresenter extends BasePresenter {
	/** @var object */
    private $record;
	/** @var Text */
	private $model;
	/** @var TextFormFactory @inject */
	public $factory;

	protected function startup() {
		parent::startup();
		$this->model = $this->text;
	}

	protected function createComponentForm() {
		$form = $this->factory->create($this->record);

		$form->onSuccess[] = function ($form) {
			$this->flashMessage("Texty byly uloženy", 'success');
			$form->getPresenter()->redirect('Text:servicesSection');
		};

		return $form;
	}
}