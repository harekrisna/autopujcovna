<?php

namespace RadekDostal\NetteComponents\DateTimePicker;
use Nette\Forms\Container;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\TextInput;
use Nette\Forms\Controls\SelectBox;
use Nette\Utils\DateTime;
use Nette\Utils\Html;
use Tracy\Debugger;

class DateTimePicker extends \Nette\Forms\Controls\BaseControl {

    // @var string
    private $date, $time;

    public function __construct($label = NULL) {
        parent::__construct($label);
    }

    public function setValue($value) {
        if($value) {
            $datetime = DateTime::from($value);
            $this->date = $datetime->format("d.m.Y");
            $this->time = $datetime->format("h:i:00");
        }
        else {
            $this->date = $this->time = NULL;
        }
    }

    public function getValue() {
        return strlen($this->date) > 0 
            ? DateTime::from($this->date." ".$this->time)
            : NULL;
    }

    public function loadHttpData() {
        $this->date = $this->getHttpData(Form::DATA_LINE, '[date]');
        $this->time = $this->getHttpData(Form::DATA_LINE, '[time]');
    }

    public function getControl() {
        $name = $this->getHtmlName();
        $date_input = Html::el('input')->name($name.'[date]')->value($this->date)->class("datetimepicker form-control small")->readonly("readonly");
        if($this->isRequired())
        	$date_input->addAttributes(['required' => ""]);
        
        $template = '<div class="input-group date" style="width: 200px; float: left; margin-right: 10px">
                         <span class="input-group-addon"><i class="fa fa-calendar"></i></span>'.$date_input.'
                     </div>
                     <div class="input-group clockpicker" data-autoclose="true" style="width: 140px; float: left;">
                         <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                         <select class="form-control" name="'.$name.'[time]" readonly="readonly">';

        for($i = 0; $i < 24; $i++) {
            $hour = str_pad($i, 2, '0', STR_PAD_LEFT);

            $template .= '<option value="'.$hour.':00:00"';

            if($hour.":00:00" == $this->time)
                $template .= " selected";

            $template .= '>'.$hour.':00</option>';

            $template .= '<option value="'.$hour.':30:00"';

            if($hour.":30:00" == $this->time)
                $template .= " selected";

            $template .= '>'.$hour.':30</option>';
        }

        $template .= '</select>
                  </div>';

        return $template;
    }

    public static function register($format = NULL) {
        Container::extensionMethod('addDateTimePicker', function($container, $name, $label = NULL) {
            $picker = $container[$name] = new DateTimePicker($label);
            return $picker;
        });
    }
}