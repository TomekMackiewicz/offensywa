<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Page;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

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
        
        return $this->render('page/admin-index.html.twig', array(
            'pages' => $pages,
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
            
            $this->addFlash("success", ucfirst($this->get('translator')->trans('crud.new.success')));

            return $this->redirectToRoute('admin_page_index');
        } else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash("danger", ucfirst($this->get('translator')->trans('crud.new.error')));
        }

        return $this->render('page/new.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a page entity.
     *
     * @Route("informacje/{slug}", name="page_show")
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
        $editForm = $this->createForm('AppBundle\Form\PageType', $page);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            
            $this->addFlash("success", ucfirst($this->get('translator')->trans('crud.edit.success')));

            return $this->redirectToRoute('page_edit', array('id' => $page->getId()));
        } else if($editForm->isSubmitted() && !$editForm->isValid()) {
            $this->addFlash("danger", ucfirst($this->get('translator')->trans('crud.edit.error')));
        }

        return $this->render('page/edit.html.twig', array(
            'page' => $page,
            'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a page entity.
     *
     * @Route("admin/pages/", name="page_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $pages = $request->request->get('pages');
        
        foreach($pages as $page) {
            $to_delete = $em->getRepository('AppBundle:Page')->findOneById((int) $page);           
            $em->remove($to_delete);
        }
        
        $em->flush();            
        $this->addFlash("success", ucfirst($this->get('translator')->trans('crud.delete.success')));

        return $this->redirectToRoute('admin_page_index');
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
