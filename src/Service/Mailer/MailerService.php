<?php

namespace App\Service\Mailer;

use App\Entity\Runs\Runs;
use App\Entity\Status\Status;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MailerService extends AbstractController
{
    public function __construct(
        private MailerInterface $mailer,
    ) {}

    public function sendMailRunChecked(array $to, Runs $run): void
    {

        $email = (new Email())
            ->from('rodolphe.davidg@gmail.com');
            foreach ($to as $key => $toEmail) {
                if ($key === array_key_first($to)){
                    $email->to($toEmail);
                }else{
                    $email->addTo($toEmail);
                }
            }
            if ($run->getRefStatus()->getId() === 2) {
                $email->subject('Your run has been verified');

            } elseif ($run->getRefStatus()->getId() === 3) {
                $email->subject('Your run has been rejected');

            }

        $email->html($this->renderView('mails/runChecked.html.twig', [
            'run' => $run
        ]));

        $this->mailer->send($email);
    }
}