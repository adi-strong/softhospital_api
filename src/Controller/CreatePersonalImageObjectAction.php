<?php

namespace App\Controller;

use App\Entity\PersonalImageObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class CreatePersonalImageObjectAction extends AbstractController
{
  public function __invoke(Request $request): PersonalImageObject
  {
    $uploadedFile = $request->files->get('file');
    if (!$uploadedFile) {
      throw new BadRequestHttpException('"Le fichier" est requis');
    }

    $personalImageObject = new PersonalImageObject();
    $personalImageObject->file = $uploadedFile;

    return $personalImageObject;
  }
}
