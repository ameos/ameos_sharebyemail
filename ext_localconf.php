<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// declare plugin
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Ameos.AmeosSharebyemail',
    'share',
    [
        \Ameos\AmeosSharebyemail\Controller\ShareController::class => 'form',
    ],
    [
        \Ameos\AmeosSharebyemail\Controller\ShareController::class => 'form',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Ameos.AmeosSharebyemail',
    'link',
    [
        \Ameos\AmeosSharebyemail\Controller\ShareController::class => 'link',
    ],
    [
        \Ameos\AmeosSharebyemail\Controller\ShareController::class => 'link',
    ]
);

// template mail path
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['templateRootPaths'][1658132362] = 'EXT:ameos_sharebyemail/Resources/Private/Templates/Email';
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['partialRootPaths'][1658132362] = 'EXT:ameos_sharebyemail/Resources/Private/Partials/Email';
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['layoutRootPaths'][1658132362] = 'EXT:ameos_sharebyemail/Resources/Private/Layouts/Email';
