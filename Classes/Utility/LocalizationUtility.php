<?php

namespace Ameos\AmeosSharebyemail\Utility;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility as CoreLocalizationUtility;

class LocalizationUtility
{
    /**
     * retrieve locallanglabel from environment
     * just a wrapper should be done in a cleaner way
     * later on
     *
     * @param $label
     * @return string
     */
    public static function getLLLabel($label, $extensionKey = 'ameos_sharebyemail')
    {
            return CoreLocalizationUtility::translate($label, $extensionKey);
    }
}
