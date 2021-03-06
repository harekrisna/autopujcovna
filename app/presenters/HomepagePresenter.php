<?php

namespace App\Presenters;

use Nette;
use App\Model;
use Tracy\Debugger;
use App\Forms\VehiclesFilterFormFactory;
use App\Forms\RentalOrderFormFactory;
use App\Forms\ContactFormFactory;

class HomepagePresenter extends BasePresenter {

	/** @var VehiclesFilterFormFactory @inject */
	public $vehiclesFilterFormFactory;
	
	/** @var RentalOrderFormFactory @inject */
	public $rentalOrderFormFactory;	

	/** @var ContactFormFactory @inject */
	public $contactFormFactory;		

	public function beforeRender() {
		parent::beforeRender();

		if(!$this->isAjax()) {
			$this->template->slides = $this->slide->findAll()
						 						  ->order('position ASC');
		}

		$this->template->slides_brands = $this->slide->findAll()
													 ->group('brand_id');
	}


	public function renderSliderBrandFilter($brand_id) {
		$this->template->slides = $this->slide->findBy(['brand_id' => $brand_id])
			 						  		  ->order('position ASC');

		$this->setLayout(false);
		$this->setView('slideshow');
		$this->redrawControl('slideshow');
	}

	public function renderVehiclesList() {
		if(empty($_SESSION['filters'])) {
			$vehicles = $this->vehicle->findAll()
									  ->order('price DESC');
		}
		else {
			$vehicles = $this->vehicle->findBy(['brand_id' => $_SESSION['filters']['brand_id'],
										 		'bodywork' => $_SESSION['filters']['bodywork'],
										 		'fuel' => $_SESSION['filters']['fuel'],
										 		'transmission' => $_SESSION['filters']['transmission']]);

			if(empty($_SESSION['order_by'])) {
				$vehicles = $vehicles->order('brand_id, type');
			}
			else {
				$vehicles = $vehicles->order($_SESSION['order_by']);	
			}
		}
		
		$this->template->vehicles = $vehicles;
		$this->template->paginator_pages = ceil($vehicles->count()/3);
		$this->redrawControl('main');
	}

	public function renderVehicleDetail($vehicle_id) {
		$this->template->vehicle = $this->vehicle->get($vehicle_id);
		$this['rentalOrderForm']['data']['vehicle_id']->setValue($vehicle_id);
		$this['rentalOrderForm']->setDefaults([]);
		/*$this->template->values = array("name" => "Michal",
										"surname" => "Bárta",
										"email" => "barta.michal@allrisk.cz",
										"phone" => "+420 728 748 246",
										"give_place" => "Brno",
										"give_time" => "08.06.2017",
										"take_place" => "Praha",
										"take_time" => "09.06.2017",
										"note" => "něco něco blabla");*/
		$this->redrawControl('main');
	}

	public function handleSetOrderBy($order_by) {
		$order_by == "price" ? $_SESSION['order_by'] = "price DESC" : "";
		$order_by == "title" ? $_SESSION['order_by'] = "brand_id.title ASC, type ASC" : "";
		$this->setView("vehiclesList");
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
			$this->template->success = true;
			$this->template->values = $values->data;

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

	protected function createComponentContactForm() {
		$form = $this->contactFormFactory->create();

		$form->onSuccess[] = function ($form, $values) {
	        $this->flashMessage('Váše zpráva byla odeslána. Děkujeme.', 'success');
			$this->redirect('vehiclesList');
		};
		
		return $form;
    }
}
