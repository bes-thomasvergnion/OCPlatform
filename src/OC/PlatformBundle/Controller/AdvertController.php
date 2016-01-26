<?php
// src/OC/PlatformBundle/Controller/AdvertController.php
namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Form\AdvertType;

class AdvertController extends Controller
{
  public function indexAction($page)
  {
    if ($page < 1) {
      throw $this->createNotFoundException("La page ".$page." n'existe pas.");
    }
    $nbPerPage = 5;
    
    $listAdverts = $this->getDoctrine()
     ->getManager()
     ->getRepository('OCPlatformBundle:Advert')
     ->getAdverts($page, $nbPerPage)
    ;
    $nbPages = ceil(count($listAdverts)/$nbPerPage);
    
    if ($page > $nbPages) {
      throw $this->createNotFoundException("La page ".$page." n'existe pas.");
    }
    return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
      'listAdverts' => $listAdverts,
      'nbPages'     => $nbPages,
      'page'        => $page
    ));
  }

  public function viewAction($id)
  {
    $em = $this->getDoctrine()->getManager();
    $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);
    
    if ($advert === null)
    {
        throw $this->createNotFoundException("L'annonce d'id ".$id." n'existe pas.");
    }
    $listAdvertSkills = $em->getRepository('OCPlatformBundle:AdvertSkill')->findByAdvert($advert);
    
    return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
       'advert' => $advert,
       'listAdvertSkills' => $listAdvertSkills,
    ));    
  }

  public function addAction(Request $request)
  {
    $advert = new Advert();
    $form = $this->createForm(AdvertType::class, $advert);

    if ($form->handleRequest($request)->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($advert);
      $em->flush();
      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
      return $this->redirect($this->generateUrl('oc_platform_view', array('id' => $advert->getId())));
    }
    return $this->render('OCPlatformBundle:Advert:add.html.twig', array(
      'form' => $form->createView()
    ));
  }
  
  public function editAction($id, Request $request)
  {
    $em = $this->getDoctrine()->getManager();
    $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);
    $form = $this->createForm(AdvertType::class, $advert);
    if ($advert == null)
    {
        throw $this->createNotFoundException("L'annoce d'id ".$id." n'existe pas.");     
    }
    if ($form->handleRequest($request)->isValid())
    {
        $em->flush();
        $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');
        return $this->redirect($this->generateUrl('oc_platform_view', array('id' => $advert->getId())));
    }
    
    return $this->render('OCPlatformBundle:Advert:edit.html.twig', array(
      'form'   => $form->createView(),
      'advert' => $advert
    ));
  }

  public function deleteAction($id, Request $request)
  {
    $em = $this->getDoctrine()->getManager();
    $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);
    if ($advert == null) 
    {
      throw $this->createNotFoundException("L'annonce d'id ".$id." n'existe pas.");
    }
    $form = $this->createFormBuilder()->getForm();
    
    if ($form->handleRequest($request)->isValid()) {
        $em->remove($advert);
        $em->flush();
        $request->getSession()->getFlashBag()->add('info', "L'annonce a bien été supprimée.");
        return $this->redirect($this->generateUrl('oc_platform_home'));
    }
    return $this->render('OCPlatformBundle:Advert:delete.html.twig', array(
      'advert' => $advert,
      'form'   => $form->createView()
    ));
    }
  public function menuAction($limit = 3)
  {
    $listAdverts = $this->getDoctrine()
      ->getManager()
      ->getRepository('OCPlatformBundle:Advert')
      ->findBy(
        array(),                 
        array('date' => 'desc'), 
        $limit,                  
        0                        
    );
    return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
      'listAdverts' => $listAdverts
    ));
  }
}
?>