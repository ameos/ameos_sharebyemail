<?php

namespace Ameos\AmeosSharebyemail\Form;

use TYPO3\CMS\Extbase\Mvc\Request;
use Ameos\AmeosForm\Form\AbstractForm;
use Ameos\AmeosForm\Form\Factory;
use Ameos\AmeosSharebyemail\Utility\LocalizationUtility;

class Form
{
    /**
     * build form
     *
     * @return self
     */
    protected function build(): self
    {
        if (is_null($this->form)) {
            $user = $GLOBALS["TSFE"]->fe_user->user;
            $this->form = \Ameos\AmeosForm\Form\Factory::make('tx_ameossharebyemail_share');
            $this->form
                ->add(
                    'name',
                    'text',
                    [
                        'defaultValue' => ($user['name']) ? $user['name'] : ''
                    ]
                )
                ->add(
                    'sender',
                    'email',
                    [
                        'required' => 'required',
                        'errorclass' => 'hasError',
                        'defaultValue' => ($user['email']) ? $user['email'] : ''
                    ]
                )
                ->add(
                    'recipient',
                    'email',
                    [
                        'required' => 'required',
                        'errorclass' => 'hasError'
                    ]
                )
                ->add(
                    'message',
                    'textarea',
                    [
                        'errorclass' => 'hasError',
                        'required' => 'required'
                    ]
                )
                ->add(
                    'captcha',
                    'captcha',
                    [
                        'errorclass' => 'hasError',
                        'required' => 'required',
                        'errormessage' => LocalizationUtility::getLLLabel('form.fields.captcha.errors.required')
                    ]
                )
                ->add(
                    'submit',
                    'submit',
                    [
                        'label' => LocalizationUtility::getLLLabel('form.fields.submit.label')
                    ]
                );
        }
        return $this;
    }

    /**
     * build constraints
     *
     * @return self
     */
    protected function buildConstraint(): self
    {
        $this->getForm()
            ->validator(
                'sender',
                'required',
                LocalizationUtility::getLLLabel('form.fields.sender.errors.required')
            )
            ->validator(
                'sender',
                'email',
                LocalizationUtility::getLLLabel('form.fields.sender.errors.email')
            )
            ->validator(
                'recipient',
                'required',
                LocalizationUtility::getLLLabel('form.fields.recipient.errors.required')
            )
            ->validator(
                'recipient',
                'email',
                LocalizationUtility::getLLLabel('form.fields.recipient.errors.email')
            );
        return $this;
    }

    /**
     * return form for render
     *
     * @return AbstractForm
     */
    public function getForm(): AbstractForm
    {
        return $this->build()->form;
    }

    /**
     * validate form
     *
     * @param  Request $request
     * @return bool
     */
    public function validate(Request $request): bool
    {
        if (!$this->getForm()->isSubmitted()) {
            return false;
        }

        $this->getForm()->bindRequest($request);
        $this->buildConstraint();
        return $this->form->isValid();
    }
}
