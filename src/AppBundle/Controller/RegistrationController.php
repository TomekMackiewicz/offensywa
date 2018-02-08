<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController extends BaseController
{    
    public function registerAction(Request $request)
    {
        /** @var $formFactory FactoryInterface */
        $formFactory = $this->get('fos_user.registration.form.factory');
        /** @var $userManager UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(false);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid() && $this->captchaverify($request->get('g-recaptcha-response'))) {
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

                $userManager->updateUser($user);

                /*****************************************************
                 * Add new functionality (e.g. log the registration) *
                 *****************************************************/
                $this->container->get('logger')->info(
                    sprintf("New user registration: %s", $user)
                );

                if (null === $response = $event->getResponse()) {
                    $url = $this->generateUrl('fos_user_registration_confirmed');
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));
                
                // Notify about new user and send email
                $notification = $this->get('notification');
                $notification->addUserNotification($user);                
                $this->newUserEmail($user);
                
                return $response;
            }

            if($form->isSubmitted() && $form->isValid() && !$this->captchaverify($request->get('g-recaptcha-response'))) {                
                $this->addFlash(
                    'error',
                    'Udowodnij, że nie jesteś robotem ;)'
                );             
            }            
            
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_FAILURE, $event);

            if (null !== $response = $event->getResponse()) {
                return $response;
            }
        }

        return $this->render('@FOSUser/Registration/register.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    # get success response from recaptcha and return it to controller
    private function captchaverify($recaptcha)
    {
        $url = "https://www.google.com/recaptcha/api/siteverify";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            "secret"=>"6LdYYEIUAAAAADNgd3HSnxt2KpITfRxRcBIas1W5","response"=>$recaptcha));
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response);     
        
        return $data->success;        
    }
    
    private function newUserEmail($user) 
    {       
        $sender = $this->getParameter('mailer_user');
        $senderName = $this->getParameter('mailer_user_name');
        $receiver = $this->getParameter('admin_mail');
        $body = 'Nowy użytkownik ' . $user->getUsername() . ' prosi o zatwierdzenie konta';
        $message = \Swift_Message::newInstance()
            ->setSubject('Nowy użytkownik')    
            ->setFrom(array($sender => $senderName))
            ->setTo($receiver)
            ->setBody(
                $this->renderView(
                    'Emails/default.html.twig', array('body' => $body)), 
                    'text/html'
            );
                
        $this->get('mailer')->send($message);
        
    }
    
}
