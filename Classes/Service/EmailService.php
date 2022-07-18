<?php

namespace Ameos\AmeosSharebyemail\Service;

use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\Mailer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Symfony\Component\Mime\Address;

class EmailService
{
    /**
     * send function
     * @param array $settings
     * @param array $data
     *
     */
    public function send(array $settings, array $data)
    {
        $data['link'] = urldecode($data['link']);
        $email = GeneralUtility::makeInstance(FluidEmail::class);
        $email
            ->to(new Address($data['recipient'], $data['recipient']))
            ->from(new Address($settings['sender']['email'], $settings['sender']['name']))
            ->subject($settings['subject'])
            ->format('html')
            ->setTemplate('ShareByEmailMessage')
            ->assignMultiple($data);
        GeneralUtility::makeInstance(Mailer::class)->send($email);
    }
}
