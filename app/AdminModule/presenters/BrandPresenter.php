<?php

namespace App\AdminModule\Presenters;

use Nette;
use App\Model;
use Tracy\Debugger;
use Nette\Application\UI\Form;
use App\AdminModule\Forms\BrandFormFactory;


class BrandPresenter extends BasePresenter {
	/** @var object */
    private $record;
	/** @var Barand */
	private $model;
	/** @var BrandFormFactory @inject */
	public $factory;

	protected function startup() {
		parent::startup();
		$this->model = $this->brand;
	}
	
	public function renderAdd() {
		$this->setView("form");
		$this->template->form_title = "Přidat značku";
	}

	public function renderEdit($record_id) {
		$this->setView("form");
		$this->template->form_title = "Upravit značku";
	}

	public function actionEdit($record_id) {
		$this->record = $this->model->get($record_id);
		
		if (!$this->record)
            throw new Nette\Application\BadRequestException("Značka nenalezena.");
			
        $this->template->record = $this->record;
	}

	public function renderList($category_id) {
        $this->template->records = $this->model->findAll();
	}

	protected function createComponentForm() {
		$form = $this->factory->create($this->record);
		
		$form->onSuccess[] = function ($form) {
			if($form->isSubmitted()->name == "add") {
				$this->flashMessage("Značka byla úspěšně přidána", 'success');
				$form->getPresenter()->redirect('Brand:add');
			}
			else {
				$this->flashMessage("Značka byla upravena", 'success');
				$form->getPresenter()->redirect('Brand:list');
			}
		};

		return $form;
	}	

	public function actionDelete($id) {
		$this->payload->success = $this->model->delete($id);
		$this->sendPayload();
	}
}
