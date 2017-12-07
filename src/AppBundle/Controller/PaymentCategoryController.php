<?php

namespace AppBundle\Controller;

use AppBundle\Entity\PaymentCategory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Paymentcategory controller.
 *
 * @Route("admin/payment-category")
 */
class PaymentCategoryController extends Controller
{
    /**
     * Lists all paymentCategory entities.
     *
     * @Route("/", name="paymentcategory_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $paymentCategories = $em->getRepository('AppBundle:PaymentCategory')->findAll();
        $deleteForms = array();
        foreach($paymentCategories as $paymentCategory) {
            $deleteForms[$paymentCategory->getId()] = $this->createDeleteForm($paymentCategory)->createView();
        }
        
        return $this->render('paymentcategory/index.html.twig', array(
            'paymentCategories' => $paymentCategories,
            'deleteForms' => $deleteForms,
        ));
    }

    /**
     * Creates a new paymentCategory entity.
     *
     * @Route("/new", name="paymentcategory_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $paymentCategory = new Paymentcategory();
        $form = $this->createForm('AppBundle\Form\PaymentCategoryType', $paymentCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($paymentCategory);
            $em->flush();
            
            $this->addFlash("success", "Nowy typ płatności dodany");

            return $this->redirectToRoute('paymentcategory_index');
            
        } else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash("danger", "Błąd podczas dodawania kategorii płatności");
        }

        return $this->render('paymentcategory/new.html.twig', array(
            'paymentCategory' => $paymentCategory,
            'form' => $form->createView(),
        ));
    }

//    /**
//     * Finds and displays a paymentCategory entity.
//     *
//     * @Route("/{id}", name="paymentcategory_show")
//     * @Method("GET")
//     */
//    public function showAction(PaymentCategory $paymentCategory)
//    {
//        $deleteForm = $this->createDeleteForm($paymentCategory);
//
//        return $this->render('paymentcategory/show.html.twig', array(
//            'paymentCategory' => $paymentCategory,
//            'delete_form' => $deleteForm->createView(),
//        ));
//    }

    /**
     * Displays a form to edit an existing paymentCategory entity.
     *
     * @Route("/{id}/edit", name="paymentcategory_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, PaymentCategory $paymentCategory)
    {
        $deleteForm = $this->createDeleteForm($paymentCategory);
        $editForm = $this->createForm('AppBundle\Form\PaymentCategoryType', $paymentCategory);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            
            $this->addFlash("success", "Kategoria płatności została uaktualniona");

            return $this->redirectToRoute('paymentcategory_edit', array('id' => $paymentCategory->getId()));
                        
        } else if($editForm->isSubmitted() && !$editForm->isValid()) {
            $this->addFlash("danger", "Błąd podczas uaktualniania kategorii");
        }

        return $this->render('paymentcategory/edit.html.twig', array(
            'paymentCategory' => $paymentCategory,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a paymentCategory entity.
     *
     * @Route("/{id}", name="paymentcategory_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, PaymentCategory $paymentCategory)
    {
        $form = $this->createDeleteForm($paymentCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($paymentCategory);
            $em->flush();
            
            $this->addFlash("success", "Kategoria usunięta");
            
        } else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash("danger", "Błąd podczas usuwania kategorii płatności");
        }

        return $this->redirectToRoute('paymentcategory_index');
    }

    /**
     * Creates a form to delete a paymentCategory entity.
     *
     * @param PaymentCategory $paymentCategory The paymentCategory entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PaymentCategory $paymentCategory)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('paymentcategory_delete', array('id' => $paymentCategory->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
