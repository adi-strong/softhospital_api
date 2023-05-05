<?php

namespace App\Services;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class MailService
{
  private const EMAIL_FROM = 'med.center@saintandremedicalcenter.org';

  public function __construct(private readonly MailerInterface $mailer)
  {
  }

  /**
   * @throws TransportExceptionInterface
   */
  public function sendMail(string $to, string $subject, string $status, array $context = []): void
  {
    $email = (new TemplatedEmail())
      ->from(new Address(self::EMAIL_FROM))
      ->to($to)
      ->subject($subject);

    switch ($status) {
      case 'nrp':
        $email
          ->htmlTemplate('emails/email_reset_pass_notifier.html.twig')
          ->context($context);
        break;
      case 'rp':
        $email
          ->htmlTemplate('emails/email_reset_password_success.html.twig')
          ->context($context);
        break;
      default:
        break;
    }

    $this->mailer->send($email);
  }
}
