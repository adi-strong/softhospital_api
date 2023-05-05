<?php

namespace App\Controller;

use App\Entity\ResetPassNotifier;
use App\Services\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
final class ResetPasswordController extends AbstractController
{
  public function __construct(
    private readonly EntityManagerInterface $em,
    private readonly RequestStack $stack,
    private readonly UserPasswordHasherInterface $encoder,
    private readonly MailService $mail)
  {
  }

  /**
   * @throws Exception
   * @throws TransportExceptionInterface
   */
  #[Route('/api/reset_pass_notifier/{id}', requirements: [ 'id' => '\d+' ], methods: ['GET'])]
  public function resetPassword($id): JsonResponse
  {
    $request = $this->stack->getCurrentRequest();

    $data = json_decode($request->getContent(), true);
    $password = $data['password'] ?? null;

    $repo = $this->em->getRepository(ResetPassNotifier::class);
    $notifier = $repo->find($id);
    if (!$notifier) throw new NotFoundHttpException('Désolé, Le code n\'existe pas.');

    $user = $notifier->getUser();
    if (!$password) throw new Exception('Le mot de passe doit être renseigné.');

    $user->setPassword($this->encoder->hashPassword($user, $password));

    $this->mail->sendMail(
      $notifier->getEmail(),
      'SH: Mot de passe réinitialisé.',
      'rp',
      ['password' => $password]);

    $this->em->remove($notifier);
    $this->em->flush();

    return new JsonResponse($user);
  }
}
