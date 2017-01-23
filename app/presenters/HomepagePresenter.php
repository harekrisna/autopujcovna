<?php

namespace App\Presenters;

use Nette;
use App\Model;
use Tracy\Debugger;
use App\Forms\VehiclesFilterFormFactory;
use App\Forms\RentalOrderFormFactory;

class HomepagePresenter extends BasePresenter {

	/** @var VehiclesFilterFormFactory @inject */
	public $vehiclesFilterFormFactory;
	
	/** @var RentalOrderFormFactory @inject */
	public $rentalOrderFormFactory;	

	public function renderVehiclesList() {
		if(empty($_SESSION['filters'])) {
			$this->template->vehicles = $this->vehicle->findAll()
													  ->order('brand_id');
		}
		else {
			$this->template->vehicles = $this->vehicle->findBy(['brand_id' => $_SESSION['filters']['brand_id'],
														 		'bodywork' => $_SESSION['filters']['bodywork'],
														 		'fuel' => $_SESSION['filters']['fuel'],
														 		'transmission' => $_SESSION['filters']['transmission']])
													  ->order('brand_id');
			$this->redrawControl('main');
		}
	}
	
	public function renderVehicleDetail($vehicle_id) {
		$this->template->vehicle = $this->vehicle->get($vehicle_id);
		$this['rentalOrderForm']['data']['vehicle_id']->setValue($vehicle_id);
		$this['rentalOrderForm']->setDefaults([]);
		$this->redrawControl('main');
	}

	protected function createComponentVehiclesFilterForm() {
		$form = $this->vehiclesFilterFormFactory->create();

		$form->onSuccess[] = function ($form, $values) {			
			$_SESSION['filters']['brand_id'] = $values->brands;
			$_SESSION['filters']['bodywork'] = $values->bodywork;
			$_SESSION['filters']['fuel'] = $values->fuel;
			$_SESSION['filters']['transmission'] = $values->transmission;													  	
			$this->setView("vehiclesList");
		};
		
		return $form;
	}
	
	protected function createComponentRentalOrderForm() {
		$form = $this->rentalOrderFormFactory->create();

		$form->onSuccess[] = function ($form, $values) {
			$this->flashMessage("Rezervace byla přijata", 'success');
			
			if(!$this->isAjax()) {
				$this->redirect($this->getAction(), $this->getParameter('vehicle_id'));
			}
			else {
				$form->setValues([], true);
				$this->redrawControl('flashes');
			}
		};
		
		return $form;
	}	
}
