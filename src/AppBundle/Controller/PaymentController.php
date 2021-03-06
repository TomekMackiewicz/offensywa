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
        
        return $this->render('payment/index.html.twig', array(
            'payments' => $payments,
        ));
    }

    /**
     * Show payments graphs entities by type.
     *
     * @Route("/all", name="payment_all")
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
            
            $this->addFlash("success", ucfirst($this->get('translator')->trans('crud.new.success'))); 

            return $this->redirectToRoute('payment_index');
            
        } else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash("danger", ucfirst($this->get('translator')->trans('crud.new.error')));
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
        $editForm = $this->createForm('AppBundle\Form\PaymentType', $payment);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();            
            $this->addFlash("success", ucfirst($this->get('translator')->trans('crud.edit.success')));

            return $this->redirectToRoute('payment_edit', array('id' => $payment->getId()));
            
        } else if($editForm->isSubmitted() && !$editForm->isValid()) {
            $this->addFlash("danger", ucfirst($this->get('translator')->trans('crud.edit.error')));
        }

        return $this->render('payment/edit.html.twig', array(
            'payment' => $payment,
            'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a payment entity / entities.
     *
     * @Route("/", name="payment_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $payments = $request->request->get('payments');
        
        foreach($payments as $payment) {
            $to_delete = $em->getRepository('AppBundle:Payment')->findOneById((int) $payment);           
            $em->remove($to_delete);
        }
        
        $em->flush();            
        $this->addFlash("success", ucfirst($this->get('translator')->trans('crud.delete.success'))); 

        return $this->redirectToRoute('payment_index');
    }
   
    private function getPaymentsForLastMonths($categories)
    {
        $em = $this->getDoctrine()->getManager();
        $currentDate = date('Y-m-d');
        $firstDay = date("Y-m-01", strtotime($currentDate . '-11 months'));
        $lastDay = date("Y-m-t", strtotime($currentDate));        
        
        $output = [];
        
        foreach($categories as $category) {
            $payments = $em->getRepository('AppBundle:Payment')->getAllTypePaymentsForLastMonths($firstDay, $lastDay, $category->getId());        
            $months = [];
            $res = [];
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
            $output[$category->getId()][] = $category->getName();
            $output[$category->getId()][] = $res;
        }
        
        return $output;
    }    
    
}