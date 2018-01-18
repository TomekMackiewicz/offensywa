<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Payment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Payment controller.
 *
 * @Route("admin/payments")
 */
class PaymentController extends Controller
{
    /**
     * Lists all payment entities.
     *
     * @Route("/", name="payment_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $payments = $em->getRepository('AppBundle:Payment')->findAll();
        $deleteForms = array();
        foreach($payments as $payment) {
            $deleteForms[$payment->getId()] = $this->createDeleteForm($payment)->createView();
        }
        
        return $this->render('payment/index.html.twig', array(
            'payments' => $payments,
            'deleteForms' => $deleteForms,
        ));
    }

    /**
     * Show payments graphs entities by type.
     *
     * @Route("/al", name="payment_all")
     * @Method("GET")
     */
    public function allAction()
    {
        $em = $this->getDoctrine()->getManager();
        $payments = $em->getRepository('AppBundle:Payment')->findAll();
        $playersCount = $em->getRepository('AppBundle:Player')->countPlayers();
        $categories = $em->getRepository('AppBundle:Payment')->getPaymentCategories();
        $paymentTypes = $this->getPaymentsForLastMonths($categories);
        
        return $this->render('payment/all.html.twig', array(
            'payments' => $payments,
            'playersCount' => $playersCount,
            'paymentTypes' => $paymentTypes
        ));
    }    
    
    /**
     * Creates a new payment entity.
     *
     * @Route("/new", name="payment_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $payment = new Payment();
        $form = $this->createForm('AppBundle\Form\PaymentType', $payment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($payment);
            $em->flush();
            
            $this->addFlash("success", "Płatność została dodana");

            return $this->redirectToRoute('payment_index');
            
        } else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash("danger", "Błąd podczas dodawania płatności");
        }

        return $this->render('payment/new.html.twig', array(
            'payment' => $payment,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing payment entity.
     *
     * @Route("/{id}/edit", name="payment_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Payment $payment)
    {
        $deleteForm = $this->createDeleteForm($payment);
        $editForm = $this->createForm('AppBundle\Form\PaymentType', $payment);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            
            $this->addFlash("success", "Płatność została uaktualniona");

            return $this->redirectToRoute('payment_edit', array('id' => $payment->getId()));
            
        } else if($editForm->isSubmitted() && !$editForm->isValid()) {
            $this->addFlash("danger", "Błąd podczas uaktualniania płatności");
        }

        return $this->render('payment/edit.html.twig', array(
            'payment' => $payment,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a payment entity.
     *
     * @Route("/{id}", name="payment_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Payment $payment)
    {
        $form = $this->createDeleteForm($payment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($payment);
            $em->flush();
            
            $this->addFlash("success", "Płatność została usunięta");
        } else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash("danger", "Błąd podczas usuwania płatności");
        }

        return $this->redirectToRoute('payment_index');
    }

    /**
     * Creates a form to delete a payment entity.
     *
     * @param Payment $payment The payment entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Payment $payment)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('payment_delete', array('id' => $payment->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    private function getPaymentsForLastMonths($categories)
    {
        $em = $this->getDoctrine()->getManager();
        $currentDate = date('Y-m-d');
        $firstDay = date("Y-m-01", strtotime($currentDate . '-11 months'));
        $lastDay = date("Y-m-t", strtotime($currentDate));        
        
        $test = [];
        
        foreach($categories as $category) {
            $payments = $em->getRepository('AppBundle:Payment')->getAllTypePaymentsForLastMonths($firstDay, $lastDay, $category->getId());        
            $months = [];
            $res = [];
            //$res[] = $category->getName();
            $i = date("Y-m", strtotime($firstDay));

            while($i <= date("Y-m", strtotime($lastDay))) {
                $months[] = $i;             
                if(substr($i, 5, 2) == "12") {
                    $i = date("Y-m", strtotime($i . "+1 month"));                
                } else {
                    $i++;
                }                
            }     

            foreach($months as $month) {
                $res[] = ["total" => 0, "period" => new \DateTime($month)];
            }

            foreach($res as &$r) {
                if($r['total'] > 0) {
                    continue;
                }            
                foreach($payments as $payment) {
                    if($r['period']->format('Y-m') == $payment['period']->format('Y-m')) {
                        $r['total'] = $payment['total'];
                    }
                }
            }
            $test[$category->getId()][] = $category->getName();
            $test[$category->getId()][] = $res;
        }

        
        return $test;
    }    
    
}