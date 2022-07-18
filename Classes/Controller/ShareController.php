<?php

namespace Ameos\AmeosSharebyemail\Controller;

use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Frontend\Utility\CanonicalizationUtility;
use Ameos\AmeosSharebyemail\Form\Form;
use Ameos\AmeosSharebyemail\Utility\LocalizationUtility;
use Ameos\AmeosSharebyemail\Service\EmailService;

class ShareController extends ActionController
{
    /**
     * form action
     *
     * @return Response
     */
    public function formAction(): Response
    {
        if ($this->request->hasArgument('link')) {
            $link = $this->request->getArgument('link');
            if ($this->isLinkCorrect($link)) {
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
     * Check if link is correct
     *
     * @param string $link
     * @return bool
     */
    protected function isLinkCorrect(string $link)
    {
        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class, $siteConfiguration);
        $site = $siteFinder->getSiteByPageId($GLOBALS['TSFE']->id);
        if (filter_var($link, FILTER_VALIDATE_URL) && strpos(urldecode($link), $site->getBase()->getHost()) > '0') {
            return true;
        }
        return false;
    }

    /**
     * link action
     *
     * @return ResponseInterface
     */
    public function linkAction()
    {
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
