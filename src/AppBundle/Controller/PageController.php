<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Page;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Page controller.
 */
class PageController extends Controller
{
    /**
     * Lists all page entities.
     *
     * @Route("admin/pages", name="admin_page_index")
     * @Method("GET")
     */
    public function adminIndexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $pages = $em->getRepository('AppBundle:Page')->findAll();
        $deleteForms = array();
        foreach($pages as $page) {
            $deleteForms[$page->getId()] = $this->createDeleteForm($page)->createView();
        }
        
        return $this->render('page/admin-index.html.twig', array(
            'pages' => $pages,
            'deleteForms' => $deleteForms
        ));
    }

    /**
     * Creates a new page entity.
     *
     * @Route("admin/pages/new", name="page_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $page = new Page();
        $form = $this->createForm('AppBundle\Form\PageType', $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($page);
            $em->flush();
            
            $this->addFlash("success", "Strona została dodana");

            return $this->redirectToRoute('admin_page_index');
        }

        return $this->render('page/new.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a page entity.
     *
     * @Route("pages/{id}", name="page_show")
     * @Method("GET")
     */
    public function showAction(Page $page)
    {
        $em = $this->getDoctrine()->getManager();
        $lastMatch = $em->getRepository('AppBundle:Game')->getLastMatch();
        $nextMatch = $em->getRepository('AppBundle:Game')->getNextMatch();
        $leagueTables = $this->get('league_table')->getleagueTables();        
        
        return $this->render('page/show.html.twig', array(
            'page' => $page,
            'lastMatch' => $lastMatch,
            'nextMatch' => $nextMatch,
            'leagueTables' => $leagueTables,
        ));
    }

    /**
     * Displays a form to edit an existing page entity.
     *
     * @Route("admin/pages/{id}/edit", name="page_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Page $page)
    {
        $deleteForm = $this->createDeleteForm($page);
        $editForm = $this->createForm('AppBundle\Form\PageType', $page);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            
            $this->addFlash("success", "Strona została uaktualniona");

            return $this->redirectToRoute('page_edit', array('id' => $page->getId()));
        }

        return $this->render('page/edit.html.twig', array(
            'page' => $page,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a page entity.
     *
     * @Route("admin/pages/{id}", name="page_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Page $page)
    {
        $form = $this->createDeleteForm($page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($page);
            $em->flush();
        }

        return $this->redirectToRoute('page_index');
    }

    /**
     * Creates a form to delete a page entity.
     *
     * @param Page $page The page entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Page $page)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('page_delete', array('id' => $page->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Add teams to navbar
     *
     * @Route("/pages/navbar", name="pages_navbar")
     * @Method("GET")
     */
    public function navbarAction() 
    {
        $em = $this->getDoctrine()->getManager();
        $pages = $em->getRepository('AppBundle:Page')->findAll();

        return $this->render('partials/navbar-pages.html.twig', array(
            'pages' => $pages
        ));
    }     
    
}
