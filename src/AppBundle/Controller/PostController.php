<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Post controller.
 */
class PostController extends Controller
{
    /**
     * Lists all post entities.
     *
     * @Route("/posts", name="post_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository('AppBundle:Post')->findAll();
        $lastMatch = $em->getRepository('AppBundle:Game')->getLastMatch();
        $leagueTables = $this->getLeagueTables();

        return $this->render('post/index.html.twig', array(
            'posts' => $posts,
            'lastMatch' => $lastMatch,
            'leagueTables' => $leagueTables,            
        ));
    }

    /**
     * Lists all post entities.
     *
     * @Route("/admin/posts", name="admin_post_index")
     * @Method("GET")
     */
    public function adminIndexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository('AppBundle:Post')->findAll();
        $deleteForms = array();
        foreach($posts as $post) {
            $deleteForms[$post->getId()] = $this->createDeleteForm($post)->createView();
        }
        
        return $this->render('post/admin-index.html.twig', array(
            'posts' => $posts,
            'deleteForms' => $deleteForms,
        ));
    }    
    
    /**
     * Creates a new post entity.
     *
     * @Route("/admin/posts/new", name="post_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $post = new Post();
        $form = $this->createForm('AppBundle\Form\PostType', $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setPublishDate(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();
            
            $this->addFlash("success", "Post dodany");

            return $this->redirectToRoute('admin_post_index');
        } else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash("danger", "Błąd podczas dodawania posta");
        }

        return $this->render('post/new.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a post entity.
     *
     * @Route("/posts/{id}", name="post_show")
     * @Method("GET")
     */
    public function showAction(Post $post)
    {
        $em = $this->getDoctrine()->getManager();
        $lastMatch = $em->getRepository('AppBundle:Game')->getLastMatch();
        $leagueTables = $this->forward('AppBundle:Front:getLeagueTables');      
        $deleteForm = $this->createDeleteForm($post);

        return $this->render('post/show.html.twig', array(
            'post' => $post,
            'delete_form' => $deleteForm->createView(),
            'lastMatch' => $lastMatch,
            'leagueTables' => $leagueTables,
        ));
    }

    /**
     * Displays a form to edit an existing post entity.
     *
     * @Route("/admin/posts/{id}/edit", name="post_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Post $post)
    {
        $deleteForm = $this->createDeleteForm($post);
        $editForm = $this->createForm('AppBundle\Form\PostType', $post);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $post->setModifyDate(new \DateTime());
            $this->getDoctrine()->getManager()->flush();
            
            $this->addFlash("success", "Post został uaktualniony");
            
            return $this->redirectToRoute('post_edit', array('id' => $post->getId()));
        } else if($editForm->isSubmitted() && !$editForm->isValid()) {
            $this->addFlash("danger", "Błąd podczas edycji posta");
        }

        return $this->render('post/edit.html.twig', array(
            'post' => $post,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a post entity.
     *
     * @Route("/admin/posts/{id}", name="post_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Post $post)
    {
        $form = $this->createDeleteForm($post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush();
            
            $this->addFlash("success", "Post usunięty");
        } else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash("danger", "Błąd podczas usuwania posta");
        }

        return $this->redirectToRoute('admin_post_index');
    }

    /**
     * Creates a form to delete a post entity.
     *
     * @param Post $post The post entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Post $post)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('post_delete', array('id' => $post->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }    

    /**
     * Get league tables.
     */
    public function getleagueTables()
    {
        $tables = [];
        $em = $this->getDoctrine()->getManager();
        $years = $em->getRepository('AppBundle:Team')->getYears();
        foreach($years as $year) {
            $query = $em->getRepository('AppBundle:Game')->getLeagueTables($year);
            $statement = $em->getConnection()->prepare($query);
            $statement->bindValue('year', $year);
            $statement->execute();
            $table['table'] = $statement->fetchAll(); 
            $table['year'] = $year;
            $tables[] = $table;
        }

        return $tables;
    }  
    
}
