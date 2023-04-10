<?php

namespace App\Controller;

use App\Entity\Covenant;
use App\Entity\PdfObject;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class CreateNewCovenantContractHandler
{
  public function handle(Request $request, Covenant $covenant, EntityManagerInterface $em): void
  {
    $uploadedFile = $request->files->get('file');
    if (!$uploadedFile) {
      throw new BadRequestHttpException('"Le fichier" est requis');
    }

    $pdfObject = new PdfObject();
    $pdfObject->file = $uploadedFile;
    $em->persist($pdfObject);

    $covenant->filePath = $pdfObject->filePath;
  }
}
