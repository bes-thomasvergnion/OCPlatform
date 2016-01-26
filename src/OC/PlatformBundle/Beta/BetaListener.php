<?php
// src/OC/PlatformBundle/Beta/BetaListener.php
namespace OC\PlatformBundle\Beta;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class BetaListener
{
  protected $endDate;

  public function __construct(BetaHTML $betaHTML, $endDate)
  {
    $this->betaHTML = $betaHTML;
    $this->endDate  = new \Datetime($endDate);
  }  
  
  public function processBeta(FilterResponseEvent $event)
  {
    if (!$event->isMasterRequest()) {
      return;
    }
    $remainingDays = $this->endDate->diff(new \Datetime())->format('%d');
    if ($remainingDays <= 0) {
      return;
    }
    $response = $this->betaHTML->displayBeta($event->getResponse(), $remainingDays);
    $event->setResponse($response);
  }
}
