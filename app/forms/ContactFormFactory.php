<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Utils\DateTime;
use Latte;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use Tracy\Debugger;

class ContactFormFactory extends Nette\Object {
	/** @var FormFactory */
	private $factory;
		
	public function __construct(FormFactory $factory) {
		$this->factory = $factory;
	}

	public function create() {
		$form = $this->factory->create();
		
	    $form->addText('name', 'Jméno a přijmení', 30, 255)
             ->setRequired('Zadejte jméno prosím.');
		
		$form->addText('phone', 'Telefon', 32);

		$form->addText('email', 'Email', 30, 255)
			 ->setType('email')
			 ->setRequired('Zadejte email prosím.')
			 ->addCondition($form::FILLED)
			  	 ->addRule(\Nette\Forms\Form::EMAIL, 'Zadejte prosím platnou emailovou adresu');
		
        $form->addTextArea('message', 'Zpráva', 45, 7)
             ->addRule(Form::FILLED, 'Napište zprávu prosím.');

        $form->addSubmit('send', 'odeslat');

		$form->onSuccess[] = array($this, 'formSucceeded');
		return $form;
	}

	public function formSucceeded(Form $form, $values) {
		$latte = new Latte\Engine;
		$params = ['name' => $values['name'],
				   'phone' => $values['phone'],
				   'email' => $values['email'],
				   'message' => $values['message']
				  ];
		
        $mail = new Message;
        $mail->setFrom($values['name']." <".$values['email'].">")
             ->addTo("Autopůjčovna Allrisk <autopujcovna@allrisk.cz>")
             ->setSubject("Zpráva z webu Car Effective")
			 ->setHtmlBody($latte->renderToString('../app/templates/components/contact-email.latte', $params));
        
        $mailer = new SendmailMailer;
        $mailer->send($mail);
	}
}
