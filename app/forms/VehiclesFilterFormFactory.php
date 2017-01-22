<?php
namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Utils\DateTime;
use Tracy\Debugger;

class VehiclesFilterFormFactory extends Nette\Object {
	/** @var FormFactory */
	private $factory;
	/** @var Vehicle */
	private $vehicle;
	/** @var Brand */
	private $brand;
	/** @var RentalOrder */
	private $rental_order;
		
	public function __construct(FormFactory $factory, \App\Model\Vehicle $vehicle, \App\Model\Brand $brand) {
		$this->factory = $factory;
		$this->vehicle = $vehicle;
		$this->brand = $brand;
	}

	public function create() {
		$form = $this->factory->create();
		
		$brands = $this->brand->findAll()->fetchPairs('id', 'title');
		
		$bodyworks = $this->vehicle->findAll()->group('bodywork')
											  ->fetchPairs('bodywork', 'bodywork');

		$fuel = $this->vehicle->findAll()->group('fuel')
									     ->fetchPairs('fuel', 'fuel');

		$transmission = $this->vehicle->findAll()->group('transmission')
									  ->fetchPairs('transmission', 'transmission');
		
		$form->addCheckboxList('brands', 'Značky:', $brands);		
		$form->addCheckboxList('bodywork', 'Karosérie:', $bodyworks);
		$form->addCheckboxList('fuel', 'Palivo:', $fuel);
		$form->addCheckboxList('transmission', 'Převodovka:', $transmission);		

		if(empty($_SESSION['filters'])) {
			$form['brands']->setValue([1, 6]);
			$form['bodywork']->setValue(["Sedan", "Combi"]);
			$form['fuel']->setValue(["benzín", "nafta"]);
			$form['transmission']->setValue(["manuál"]);
		}
		else {
			$form['brands']->setValue($_SESSION['filters']['brand_id']);
			$form['bodywork']->setValue($_SESSION['filters']['bodywork']);
			$form['fuel']->setValue($_SESSION['filters']['fuel']);
			$form['transmission']->setValue($_SESSION['filters']['transmission']);
		}

		$form->onSuccess[] = array($this, 'formSucceeded');			
		$form->addSubmit('search', 'Vyhledat');

		return $form;
	}

	public function formSucceeded(Form $form, $values) {
	}
}
