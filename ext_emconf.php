<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Ameos Share By Email',
    'description' => 'Share a link by email (tipafriend replacement) Provides a sharing link and a form to send the link by email',
    'category' => 'plugin',
    'author' => 'Luc Muller',
    'author_email' => 'typo3dev@ameos.com',
    'author_company' => 'AMEOS',
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.4',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.99.99',
            'ameos_form' => '1.4.0-1.99.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
           'Ameos\\AmeosSharebyemail\\' => 'Classes'
        ]
     ],
];
