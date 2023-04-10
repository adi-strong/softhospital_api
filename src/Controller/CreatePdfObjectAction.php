<?php

namespace App\Controller;

use App\Entity\PdfObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class CreatePdfObjectAction extends AbstractController
{
  public function __invoke(Request $request): PdfObject
  {
    $uploadedFile = $request->files->get('file');
    if (!$uploadedFile) {
      throw new BadRequestHttpException('"Le fichier" est requis');
    }

    $pdfObject = new PdfObject();
    $pdfObject->file = $uploadedFile;

    return $pdfObject;
  }
}
