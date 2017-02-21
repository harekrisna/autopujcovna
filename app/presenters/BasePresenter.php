<?php

namespace App\Presenters;

use Nette;
use App\Model;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter {
	/** @var Slide */
	protected $slide;	
	/** @var Brand */
	protected $brand;	
	/** @var Vehicle */
	protected $vehicle;	
	/** @var Text */
	protected $text;
	
	protected function startup() {
		parent::startup();
		$this->slide = $this->context->getService('slide');
		$this->brand = $this->context->getService('brand');
        $this->vehicle = $this->context->getService('vehicle');
        $this->text = $this->context->getService('text');

   		\RadekDostal\NetteComponents\DateTimePicker\DateTimePicker::register();
	}


	protected function beforeRender() {
		parent::beforeRender();
		$texts = $this->text->findAll();
	    $template_texts = [];

	    foreach ($texts as $text) {
	    	$template_texts[$text->title] = $text->text;
	    }

	    $this->template->text = $template_texts;
	}
}
