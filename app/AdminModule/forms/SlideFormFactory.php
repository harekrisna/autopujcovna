<?php

namespace App\AdminModule\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Utils\FileSystem;
use Tracy\Debugger;
use Nette\Utils\Image;

class SlideFormFactory extends Nette\Object {
	/** @var FormFactory */
	private $factory;
	/** @var Brand */
	private $brand;
	/** @var Slide */
	private $model;

	private $record;
		
	public function __construct(FormFactory $factory, \App\Model\Slide $slide, \App\Model\Brand $brand) {
		$this->factory = $factory;
		$this->model = $slide;
		$this->brand = $brand;
	}

	public function create($record = null) {
		$this->record = $record;

		$form = $this->factory->create();
		$data = $form->addContainer('data');

	    $data->addText('title', 'Název:', 30, 255)
      	     ->setRequired('Zadejte prosím název slidu.');

		$brand_pairs = $this->brand->findAll()
								   ->fetchPairs('id', 'title');

		$data->addSelect('brand_id', "Značka", $brand_pairs);

		$form->addUpload('photo_file', 'Obrázek:')
			 ->addCondition($form::FILLED)
				 ->addRule($form::IMAGE, 'Obrázek musí být JPEG, PNG nebo GIF');

	    $form->addSubmit('add', 'Přidat slide');
	    $form->addSubmit('edit', 'Uložit změny');

	    if($record != null) {
	    	$form['data']->setDefaults($record);
	    }

		$form->onSuccess[] = array($this, 'formSucceeded');
		return $form;
	}

	public function formSucceeded(Form $form, $values) {
		if($form->isSubmitted()->name == "add") {
			$max_position = $this->model->findAll()
                                        ->max('position');

            $data = $values->data;
            $data['position'] = $max_position + 1;
			$new_record = $this->model->insert($values->data);

	        if($values->photo_file->isOk() && $values->photo_file->isImage()) {
	        	$photo_filename = $new_record->id."_".$values->photo_file->getSanitizedName();
				$image = $values->photo_file->toImage();
				$image->resize(NULL, 945);
				$image->save("./images/slides/".$photo_filename);
				chmod("./images/slides/".$photo_filename, 0777);

				$this->model->update($new_record->id, ['photo_file' => $photo_filename]);
	        }
		}
		else {
			if($values->photo_file->isOk() && $values->photo_file->isImage()) {
				if($this->record->photo_file != null) {
					FileSystem::delete("./images/slides/".$this->record->photo_file);
				}

				$photo_filename = $this->record->id."_".$values->photo_file->getSanitizedName();
				$image = $values->photo_file->toImage();
				$image->resize(NULL, 945);
				$image->save("./images/slides/".$photo_filename);
				chmod("./images/slides/".$photo_filename, 0777);
				
				$this->model->update($this->record->id, ['photo_file' => $photo_filename]);
			}

			$this->model->update($this->record->id, $values->data);
		}
		
	}
}
