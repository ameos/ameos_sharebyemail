<?php

namespace Ameos\AmeosSharebyemail\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Frontend\Utility\CanonicalizationUtility;

class LinkUtility
{
    /**
     * Check if link is correct
     *
     * @param  string $link
     * @return bool
     */
    public static function isLinkCorrect(string $link)
    {
        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class, $siteConfiguration);
        $site = $siteFinder->getSiteByPageId($GLOBALS['TSFE']->id);
        if (filter_var($link, FILTER_VALIDATE_URL) && strpos(urldecode($link), $site->getBase()->getHost()) > '0') {
            return true;
        }
        return false;
    }

    /**
     * get current Link
     *
     * @param  $label
     * @return string
     */
    public static function getCurrentLink()
    {
        return $GLOBALS['TSFE']->cObj->typoLink_URL(
            [
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
            ]
        );
    }
}
