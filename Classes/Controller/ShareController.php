<?php

namespace Ameos\AmeosSharebyemail\Controller;

use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Ameos\AmeosSharebyemail\Form\Form;
use Ameos\AmeosSharebyemail\Utility\LinkUtility;
use Ameos\AmeosSharebyemail\Utility\LocalizationUtility;
use Ameos\AmeosSharebyemail\Service\EmailService;

class ShareController extends ActionController
{
    /**
     * Form action
     *
     * @return Response
     */
    public function formAction(): Response
    {
        if ($this->request->hasArgument('link')) {
            $link = $this->request->getArgument('link');
            if (LinkUtility::isLinkCorrect($link)) {
                $form = GeneralUtility::makeInstance(Form::class);
                if ($form->validate($this->request)) {
                        $emailService = GeneralUtility::makeInstance(EmailService::class);
                        $emailService->send($this->settings['email'], $this->request->getArguments());

                        $this->forward('confirm');
                }
                $this->view->assignMultiple([
                    'form' => $form->getForm(),
                    'link' => filter_var(urldecode($link), FILTER_SANITIZE_URL)
                ]);
            } else {
                $this->error = LocalizationUtility::getLLLabel("errors.incorrectlink");
                $this->forward(
                    'error',
                    null,
                    null,
                    array('error' => LocalizationUtility::getLLLabel("errors.incorrectlink"))
                );
            }
        } else {
            $this->forward('error', null, null, array('error' => LocalizationUtility::getLLLabel("errors.nolink")));
        }
        return $this->htmlResponse();
    }

    /**
     * link action
     *
     * @return ResponseInterface
     */
    public function linkAction()
    {
        $currentLink = LinkUtility::getCurrentLink();
        $this->view->assign('sharePid', $this->settings['sharePid']);
        $this->view->assign('currentLink', $currentLink);

        return $this->htmlResponse();
    }

    /**
     * confirm action
     *
     * @return ResponseInterface
     */
    public function confirmAction()
    {
        return $this->htmlResponse();
    }

    /**
     * error action
     *
     * @return ResponseInterface
     */
    public function errorAction()
    {
        $this->view->assign('error', $this->request->getArgument('error'));
        return $this->htmlResponse();
    }
}
