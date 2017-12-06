<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Request AS UserRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Request controller.
 */
class RequestController extends Controller
{
    /**
     * Lists all request entities.
     *
     * @Route("/admin/requests", name="request_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $requests = $em->getRepository('AppBundle:Request')->findBy(array(), array('date' => 'DESC'));
        $deleteForms = array();
        foreach($requests as $request) {
            $deleteForms[$request->getId()] = $this->createDeleteForm($request)->createView();
        }
        
        return $this->render('request/index.html.twig', array(
            'requests' => $requests,
            'deleteForms' => $deleteForms
        ));
    }

    /**
     * Creates a new request entity.
     *
     * @Route("/user/requests/new", name="request_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $order = new UserRequest();
        $form = $this->createForm('AppBundle\Form\RequestType', $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order->setDate(new \DateTime());
            $user = $this->get('security.token_storage')
                ->getToken()
                ->getUser();            
            $order->setUser($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($order);
            $em->flush();

            $this->addFlash("success", "Zamówienie dodane");
            
            return $this->redirectToRoute('fos_user_profile_show');
        }

        return $this->render('request/new.html.twig', array(
            'request' => $request,
            'form' => $form->createView(),
        ));
    }

//    /**
//     * Finds and displays a request entity.
//     *
//     * @Route("/{id}", name="request_show")
//     * @Method("GET")
//     */
//    public function showAction(Request $request)
//    {
//        $deleteForm = $this->createDeleteForm($request);
//
//        return $this->render('request/show.html.twig', array(
//            'request' => $request,
//            'delete_form' => $deleteForm->createView(),
//        ));
//    }

//    /**
//     * Displays a form to edit an existing request entity.
//     *
//     * @Route("/{id}/edit", name="request_edit")
//     * @Method({"GET", "POST"})
//     */
//    public function editAction(Request $request, Request $request)
//    {
//        $deleteForm = $this->createDeleteForm($request);
//        $editForm = $this->createForm('AppBundle\Form\RequestType', $request);
//        $editForm->handleRequest($request);
//
//        if ($editForm->isSubmitted() && $editForm->isValid()) {
//            $this->getDoctrine()->getManager()->flush();
//
//            return $this->redirectToRoute('request_edit', array('id' => $request->getId()));
//        }
//
//        return $this->render('request/edit.html.twig', array(
//            'request' => $request,
//            'edit_form' => $editForm->createView(),
//            'delete_form' => $deleteForm->createView(),
//        ));
//    }

    /**
     * Deletes a request entity.
     *
     * @Route("/{id}", name="request_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, UserRequest $userRequest)
    {
        $form = $this->createDeleteForm($userRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($userRequest);
            $em->flush();
        }

        return $this->redirectToRoute('request_index');
    }

    /**
     * Creates a form to delete a request entity.
     *
     * @param Request $request The request entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(UserRequest $userRequest)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('request_delete', array('id' => $userRequest->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
