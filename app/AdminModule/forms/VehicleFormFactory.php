<?php

namespace App\AdminModule\Forms;

use Nette;
use Nette\Application\UI\Form;
use Tracy\Debugger;

class VehicleFormFactory extends Nette\Object {
	/** @var FormFactory */
	private $factory;
	/** @var Vehicle */
	private $vehicle;
	/** @var Brand */
	private $brand;

	private $record;
		
	public function __construct(FormFactory $factory, \App\Model\Vehicle $vehicle, \App\Model\Brand $brand) {
		$this->factory = $factory;
		$this->vehicle = $vehicle;
		$this->brand = $brand;
	}

	public function create($record = null) {
		$this->record = $record;

		$form = $this->factory->create();
		$data = $form->addContainer('data');

		$data->addText('rz', 'RZ')
			 ->setRequired('Zadejte RZ automobilu prosím.');
		
		$brand_items = $this->brand->findAll()->fetchPairs('id', 'title');
		$data->addSelect("brand_id", "Značka", $brand_items);
		
		$data->addText('type', 'Typ');
		$data->addSelect("bodywork", "Karoserie", ['Sedan' => 'Sedan', 'Liftback' => 'Liftback', 'Hatchback' => 'Hatchback', 'Limuzína' => 'Limuzína', 'Combi' => 'Combi', 'MPV' => 'MPV', 'SUV' => 'SUV', 'Crossover' => 'Crossover', 'Terénní vůz' => 'Terénní vůz', 'Pick-Up' => 'Pick-Up', 'Kabriolet' => 'Kabriolet', 'Roadster' => 'Roadster', 'Kupé' => 'Kupé']);
		$data->addSelect("fuel", "Palivo", ['benzín' => 'benzín', 'nafta' => 'nafta', 'LPG' => 'LPG', 'CNG' => 'CNG']);
		$data->addSelect("transmission", "Řazení", ['manuál' => 'manuál', 'automat' => 'automat']);

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
				$new_record = $this->vehicle->insert($values->data);
				$photos_folder = "./images/photos/".$new_record->id."_".$new_record->rz;
				mkdir($photos_folder);
				chmod($photos_folder, 0777);
				mkdir($photos_folder."/photos");
				chmod($photos_folder, 0777);
				mkdir($photos_folder."/previews");
				chmod($photos_folder, 0777);
			}
			else {
				$this->vehicle->update($this->record->id, $values->data);
			}
		}
		catch(\App\Model\DuplicateException $e) {
			if($e->foreign_key == "rz") {
				$form['data']['rz']->addError("Automobil s tímto RZ již existuje.");
			}
		}
	}
}
