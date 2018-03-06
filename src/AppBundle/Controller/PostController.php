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
     * @Route("/posty", name="post_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository('AppBundle:Post')->findAll();
        $lastMatch = $em->getRepository('AppBundle:Game')->getLastMatch();
        $nextMatch = $em->getRepository('AppBundle:Game')->getNextMatch();
        $leagueTables = $this->get('league_table')->getleagueTables();

        return $this->render('post/index.html.twig', array(
            'posts' => $posts,
            'lastMatch' => $lastMatch,
            'nextMatch' => $nextMatch,
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
        
        return $this->render('post/admin-index.html.twig', array(
            'posts' => $posts,
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
            
            $this->addFlash("success", ucfirst($this->get('translator')->trans('crud.new.success')));

            return $this->redirectToRoute('admin_post_index');
        } else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash("danger", ucfirst($this->get('translator')->trans('crud.new.error')));
        }

        return $this->render('post/new.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a post entity.
     *
     * @Route("/posty/{slug}", name="post_show")
     * @Method("GET")
     */
    public function showAction(Post $post)
    {
        $em = $this->getDoctrine()->getManager();
        $lastMatch = $em->getRepository('AppBundle:Game')->getLastMatch();
        $nextMatch = $em->getRepository('AppBundle:Game')->getNextMatch();
        $leagueTables = $this->get('league_table')->getleagueTables();

        return $this->render('post/show.html.twig', array(
            'post' => $post,
            'lastMatch' => $lastMatch,
            'nextMatch' => $nextMatch,
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
        $editForm = $this->createForm('AppBundle\Form\PostType', $post);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $post->setModifyDate(new \DateTime());
            $this->getDoctrine()->getManager()->flush();
            
            $this->addFlash("success", ucfirst($this->get('translator')->trans('crud.edit.success')));
            
            return $this->redirectToRoute('post_edit', array('id' => $post->getId()));
        } else if($editForm->isSubmitted() && !$editForm->isValid()) {
            $this->addFlash("danger", ucfirst($this->get('translator')->trans('crud.edit.error')));
        }

        return $this->render('post/edit.html.twig', array(
            'post' => $post,
            'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a post entity.
     *
     * @Route("/admin/posts/", name="post_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $posts = $request->request->get('posts');
        
        foreach($posts as $post) {
            $to_delete = $em->getRepository('AppBundle:Post')->findOneById((int) $post);           
            $em->remove($to_delete);
        }
        
        $em->flush();            
        $this->addFlash("success", ucfirst($this->get('translator')->trans('crud.delete.success')));

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
    
}
