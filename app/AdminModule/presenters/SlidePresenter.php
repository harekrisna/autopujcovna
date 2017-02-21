<?php

namespace App\AdminModule\Presenters;

use Nette;
use App\Model;
use Tracy\Debugger;
use Nette\Application\UI\Form;
use Nette\Database\SqlLiteral;
use App\AdminModule\Forms\SlideFormFactory;



class SlidePresenter extends BasePresenter {
	/** @var object */
    private $record;
	/** @var Slide */
	private $model;
	/** @var SlideFormFactory @inject */
	public $factory;

	protected function startup() {
		parent::startup();
		$this->model = $this->slide;
	}
	
	public function actionAdd() {
		$this['form']['photo_file']->setRequired('Zadejte prosím obrázek slidu.');
	}

	public function renderAdd() {
		$this->setView("form");
		$this->template->form_title = "Přidat slide";
	}

	public function renderEdit($record_id) {
		$this->setView("form");
		$this->template->form_title = "Upravit slide";
	}

	public function actionEdit($record_id) {
		$this->record = $this->model->get($record_id);
		
		if (!$this->record)
            throw new Nette\Application\BadRequestException("Slide nenalezen.");
			
        $this->template->record = $this->record;
	}

	public function renderList($category_id) {
        $this->template->records = $this->model->findAll()
        									   ->order('position ASC');
	}

	protected function createComponentForm() {
		$form = $this->factory->create($this->record);
		
		$form->onSuccess[] = function ($form) {
			if($form->isSubmitted()->name == "add") {
				$this->flashMessage("Slide byl úspěšně přidán", 'success');
				$form->getPresenter()->redirect('Slide:add');
			}
			else {
				$this->flashMessage("Slide byl upraven", 'success');
				$form->getPresenter()->redirect('Slide:list');
			}
		};

		return $form;
	}	

	public function handleUpdatePosition($record_id, $new_position) {
        $old_position = $this->model->get($record_id)
                                    ->position;
                             
        if($old_position != $new_position) {
            $max_position = $this->model->findAll()
                                        ->max('position');
            
            $this->model->get($record_id)
            			->update(['position' => $new_position]);
            			
            $sign = $old_position < $new_position ? "-" : "+";
            $this->model->findAll()
                        ->where("id != ? AND position BETWEEN ? AND ?", $record_id, min($old_position, $new_position), max($old_position, $new_position))
                        ->update(["position" => new SqlLiteral("position {$sign} 1")]);
        }
        
        $this->redirect('list');
	}

	public function handleDelete($id) {
		$record = $this->model->get($id);
		if($record->photo_file != "") {
			\Nette\Utils\FileSystem::delete(SLIDES_IMAGES_FOLDER.$record->photo_file);
		}

		$this->model->findAll()
		            ->where('position > ?', $record->position)
                    ->update(["position" => new SqlLiteral("position - 1")]);

		$this->payload->success = $this->model->delete($id);		
		$this->sendPayload();
	}
}
