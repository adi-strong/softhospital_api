<?php

namespace App\Serializer;

use App\Entity\PdfObject;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Vich\UploaderBundle\Storage\StorageInterface;

final class PdfFileObjectNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
  use NormalizerAwareTrait;

  private const ALREADY_CALLED = 'PDF_OBJECT_NORMALIZER_ALREADY_CALLED';

  public function __construct(private readonly StorageInterface $storage)
  {
  }

  public function normalize(mixed $object, ?string $format = null, array $context = [])
  : float|array|\ArrayObject|bool|int|string|null
  {
    $context[self::ALREADY_CALLED] = true;
    $object->contentUrl = $this->storage->resolveUri($object, 'file');

    return $this->normalizer->normalize($object, $format, $context);
  }

  public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
  {
    if (isset($context[self::ALREADY_CALLED])) return false;
    return $data instanceof PdfObject;
  }
}
