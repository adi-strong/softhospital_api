<?php

namespace App\Events\MedicineEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\BoxHistoric;
use App\Entity\MedicineInvoice;
use App\Entity\MedicinesSold;
use App\Repository\BoxRepository;
use App\Repository\MedicineRepository;
use App\Services\HandleCurrentUserService;
use DateTime;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class OnPostMedicineSaleEvent implements EventSubscriberInterface
{
  #[ArrayShape([KernelEvents::VIEW => "array"])] 
  public static function getSubscribedEvents(): array
  {
    return [
      KernelEvents::VIEW => [
        'handler',
        EventPriorities::PRE_WRITE,
      ]
    ];
  }
  
  public function __construct(
    private readonly HandleCurrentUserService $user, 
    private readonly EntityManagerInterface $em, 
    private readonly MedicineRepository $repository,
    private readonly BoxRepository $boxRepository)
  {
  }

  /**
   * @throws Exception
   */
  public function handler(ViewEvent $event)
  {
    $invoice = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if ($invoice instanceof MedicineInvoice && $method === Request::METHOD_POST) {
      $hospital = $this->user->getHospital() ?? $this->user->getHospitalCenter();
      $released = new DateTime();

      $invoice->setUser($this->user->getUser());
      $invoice->setReleased($released);
      $invoice->setHospital($hospital);
      
      $values = $invoice->values;
      foreach ($values as $value) {
        if (null !== $value['id']) {
          $findMedicine = $this->repository->findMedicine($value['id']);
          if (null !== $findMedicine) {
            $quantity = (float) $value['quantity'] ?? null;
            $price = $value['price'] ?? null;
            $cost = $value['cost'] ?? '0';

            if ($quantity !== null) {
              $newQty = $findMedicine->getQuantity() - $quantity;
              $sum = $quantity * $price;
              $findMedicine->setQuantity($newQty);
              
              $newSale = (new MedicinesSold())
                ->setQuantity($quantity)
                ->setMedicine($findMedicine)
                ->setPrice($price)
                ->setSum($sum)
                ->setCost($cost)
                ->setInvoice($invoice);
              $invoice->addMedicinesSold($newSale);
            }
            else throw new Exception('Quantité invalide.');
          }
          else throw new NotFoundHttpException('L\'ID '.$value['id'].'n\'existe pas.');
        }
        
      }

      $findBox = $this->boxRepository->findBox($hospital->getId());
      if (null !== $findBox) {
        $boxHistoric = (new BoxHistoric())
          ->setBox($findBox)
          ->setCreatedAt($released)
          ->setAmount($invoice->getTotalAmount())
          ->setTag('input');
        $this->em->persist($boxHistoric);
      }
      
      $this->em->flush();
      
    }
  }
}
