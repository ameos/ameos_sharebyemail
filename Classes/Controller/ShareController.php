<?php

namespace Ameos\AmeosSharebyemail\Controller;

use \TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use \TYPO3\CMS\Extbase\Object\ObjectManager;
use \TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use \TYPO3\CMS\Frontend\Utility\CanonicalizationUtility;

class ShareController extends ActionController{
	
	public function formAction(){
		if($this->request->hasArgument('link')){
			if($this->isLinkCorrect()){
				$this->view->assign('link',filter_var(urldecode($this->link),FILTER_SANITIZE_URL));
				$this->initializeForm();
			}else{
				$this->error = self::getLLLabel("errors.incorrectlink");
				$this->forward('error',null,null,array('error' => self::getLLLabel("errors.incorrectlink")));
			}
		}else{
			$this->forward('error',null,null,array('error' => self::getLLLabel("errors.nolink")));
		}
	}

	protected function initializeForm(){
		$objectManager = GeneralUtility::makeInstance(ObjectManager::class);

		$user = $GLOBALS["TSFE"]->fe_user->user;
	 	$form = \Ameos\AmeosForm\Form\Factory::make('tx_ameossharebyemail_share');
	 	$form
	 		->add('name', 'text', ['required'=>'required', 'errorclass'=>'hasError', 'defaultValue' => ($user['name']) ? $user['name']:''])
	 		->validator('name', 'required', self::getLLLabel('form.fields.name.errors.required'))
	 		->add('email', 'email', ['required'=>'required', 'errorclass'=>'hasError', 'defaultValue' => ($user['email']) ? $user['email']:''])
	 		->validator('email', 'required', self::getLLLabel('form.fields.email.errors.required'))
	 		->validator('email', 'email', self::getLLLabel('form.fields.email.errors.email'))
	 		->add('destinataire', 'email', ['required'=>'required', 'errorclass'=>'hasError'])
	 		->validator('destinataire', 'required', self::getLLLabel('form.fields.destinataire.errors.required'))
	 		->validator('destinataire', 'email', self::getLLLabel('form.fields.destinataire.errors.email'))
	 		->add('message', 'textarea', ['errorclass' => 'hasError', 'required'=>'required'])
	 		->add('captcha', 'captcha', ['errorclass' => 'hasError', 'required'=>'required', 'errormessage' => self::getLLLabel('form.fields.captcha.errors.required')])
	 		->add('submit', 'submit', ['label' => self::getLLLabel('form.fields.submit.label')]);

	 	if($form->isSubmitted()) {
			try {
				$form->bindRequest($this->request);
			} catch(\Exception $e) {
				die($e->getMessage());
			}
			if($form->isValid()) {
				$aData['name'] = $this->request->getArgument('name');
				$aData['email'] = $this->request->getArgument('email');
				$aData['destinataire'] = $this->request->getArgument('destinataire');
				$aData['message'] = $this->request->getArgument('message');
				$aData['link'] = urldecode($this->link);
				$view = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
				$templatePath = array_pop($this->view->getTemplateRootPaths()).'Mail.html';
				$view->setTemplatePathAndFilename($templatePath);


				
				$view->assignMultiple($aData);

				$mail = $this->objectManager->get('TYPO3\\CMS\\Core\\Mail\\MailMessage');
				$mail->setSubject($this->settings['email']['subject']);
				$mail->setFrom([$this->settings["email"]["sender"]["email"]=>$this->settings["email"]["sender"]["name"]]);
				$mail->setReplyTo([$aData['email']=>$aData['name']]);
				$output = $view->render();
				$mail->html($output);
				$mail->setTo([$aData['destinataire']=>$aData['destinataire']]); 
				$mail->send();
				$this->forward('confirm');
			}
		}
		$this->view->assign('form',$form);
	}

	protected function isLinkCorrect(){
		$this->link = $this->request->getArgument('link');
		$siteFinder = GeneralUtility::makeInstance(SiteFinder::class,$siteConfiguration);
		$site = $siteFinder->getSiteByPageId($GLOBALS['TSFE']->id);
		if(filter_var($this->link, FILTER_VALIDATE_URL) && strpos(urldecode($this->link),$site->getBase()->getHost()) > '0'){
			return true;
		}
		return false;
	}

	public function linkAction(){
		$currentLink = $GLOBALS['TSFE']->cObj->typoLink_URL([
            'parameter' => $GLOBALS['TSFE']->id . ',' . $GLOBALS['TSFE']->type,
            'forceAbsoluteUrl' => true,
            'addQueryString' => true,
            'addQueryString.' => [
                'exclude' => implode(
                    ',',
                    CanonicalizationUtility::getParamsToExcludeForCanonicalizedUrl(
                        (int)$GLOBALS['TSFE']->id,
                        (array)$GLOBALS['TYPO3_CONF_VARS']['FE']['additionalCanonicalizedUrlParameters']
                    )
                ),
            ],
        ]);
        $this->view->assign('sharePid',$this->settings['sharePid']);
        $this->view->assign('currentLink',$currentLink);
	}

	public function confirmAction(){

	}

	public function errorAction(){
		$this->view->assign('error',$this->request->getArgument('error'));
	}

	/**
     * retrieve locallanglabel from environment
     * just a wrapper should be done in a cleaner way
     * later on
     *
     * @param $label
     * @return string
     */
    static function getLLLabel($label) {
            return LocalizationUtility::translate($label, 'ameos_sharebyemail');
    }
}