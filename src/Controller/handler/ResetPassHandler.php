<?php

namespace App\Controller\handler;

use App\Entity\ResetPassNotifier;
use App\Repository\UserRepository;
use App\Services\MailService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class ResetPassHandler
{
  public function __construct(private readonly MailService $mail, private readonly UserRepository $repository)
  {
  }

  public function handle(ResetPassNotifier $notifier, RequestStack $stack, EntityManagerInterface $em): void
  {
    $request = $stack->getCurrentRequest();
    $data = json_decode($request->getContent(), true);
    $email = $data['email'] ?? null;
    $username = $data['username'] ?? null;

    $user = $this->repository->findOneBy(['username' => $username]);
    if (!$user) throw new NotFoundHttpException("Le compte avec le username: ".strtoupper($username)." n'existe pas.");

    $code = rand(10000, 99999);
    $notifier->setUsername($username);
    $notifier->setEmail($email);
    $notifier->setCode($code);
    $notifier->setUser($user);
    $notifier->setReleasedAt(new DateTime());

    $em->persist($notifier);
    $em->flush();

    try {
      $this->mail->sendMail(
        $notifier->getEmail(),
        'SH: Réinitialisation du mot de passe',
        'nrp',
        [
          'code' => $notifier->getCode(),
        ]);
    } catch (TransportExceptionInterface $e) { dd($e); }
  }
}
