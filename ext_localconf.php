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