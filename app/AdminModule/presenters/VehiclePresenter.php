<?php

namespace App\AdminModule\Presenters;

use Nette;
use App\Model;
use Tracy\Debugger;
use Nette\Application\UI\Form;
use App\AdminModule\Forms\VehicleFormFactory;


class VehiclePresenter extends BasePresenter {
	/** @var object */
    private $record;
	/** @var Vehicle */
	private $model;
	/** @var VehicleFormFactory @inject */
	public $factory;

	protected function startup() {
		parent::startup();
		$this->model = $this->vehicle;
	}
	
	public function renderAdd() {
		$this->setView("form");
		$this->template->form_title = "Přidat automobil";
	}

	public function renderEdit($record_id) {
		$this->setView("form");
		$this->template->form_title = "Upravit automobil";
	}

	public function actionEdit($record_id) {
		$this->record = $this->model->get($record_id);
		
		if (!$this->record)
            throw new Nette\Application\BadRequestException("Automobil nenalezen.");
			
        $this->template->record = $this->record;
	}

	public function renderList($category_id) {
        $this->template->records = $this->model->findAll();
	}

	protected function createComponentForm() {
		$form = $this->factory->create($this->record);
		
		$form->onSuccess[] = function ($form) {
			if($form->isSubmitted()->name == "add") {
				$this->flashMessage("Automobul byl úspěšně přidán", 'success');
				$form->getPresenter()->redirect('Vehicle:add');
			}
			else {
				$this->flashMessage("Automobil byl upraven", 'success');
				$form->getPresenter()->redirect('Vehicle:list');
			}
		};

		return $form;
	}	

	public function actionDelete($id) {
		$record = $this->model->get($id);
		if($record->photos_folder != "") {
			\Nette\Utils\FileSystem::delete("./images/photos/".$record->photos_folder);
		}
		
		Debugger::fireLog($id);
		$this->payload->success = $this->model->delete($id);
		$this->sendPayload();
	}
}
