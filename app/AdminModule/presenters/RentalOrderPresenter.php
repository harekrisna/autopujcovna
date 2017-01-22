<?php

namespace App\AdminModule\Presenters;

use Nette;
use App\Model;
use Tracy\Debugger;
use Nette\Application\UI\Form;
use App\AdminModule\Forms\RentalOrderFormFactory;


class RentalOrderPresenter extends BasePresenter {
	/** @var object */
    private $record;
	/** @var Vehicle */
	private $model;
	/** @var RentalOrderFormFactory @inject */
	public $factory;

	protected function startup() {
		parent::startup();
		$this->model = $this->rentalOrder;
	}
	
	public function renderAdd() {
		$this->setView("form");
		$this->template->form_title = "Vytvořit objednávku";
	}

	public function renderEdit($record_id) {
		$this->setView("form");
		$this->template->form_title = "Upravit objednávku";
	}

	public function actionEdit($record_id) {
		$this->record = $this->model->get($record_id);
		
		if (!$this->record)
            throw new Nette\Application\BadRequestException("Objednávka nenalezena.");
			
        $this->template->record = $this->record;
	}

	public function renderList($category_id) {
        $this->template->records = $this->model->findAll()
        									   ->order("created_time DESC");
	}

	protected function createComponentForm() {
		$form = $this->factory->create($this->record);
		
		if($this->record && $this->record->processed_time != null)
			$form['processed']->setDefaultValue(TRUE);

		$form->onSuccess[] = function ($form) {
			if($form->isSubmitted()->name == "add") {
				$this->flashMessage("Objednávka byla úspěšně vytvořena", 'success');
				$form->getPresenter()->redirect('RentalOrder:add');
			}
			else {
				$this->flashMessage("Objednávka byla upravena", 'success');
				$form->getPresenter()->redirect('RentalOrder:list');
			}
		};

		return $form;
	}	

	public function actionDelete($id) {
		$this->payload->success = $this->model->delete($id);
		$this->sendPayload();
	}
}
