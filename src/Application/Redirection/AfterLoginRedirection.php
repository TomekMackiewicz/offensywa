<?php

namespace Application\Redirection;

use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Translation\Translator;
use Symfony\Component\HttpFoundation\Response;

class AfterLoginRedirection implements AuthenticationSuccessHandlerInterface
{
    protected $router;
    protected $security;
    protected $twig;
    protected $translator;

    /**
     * AfterLoginRedirection constructor.
     * @param Router $router
     * @param AuthorizationChecker $security
     */
    public function __construct(Router $router, AuthorizationChecker $security, \Twig_Environment $twig, Translator $translator)
    {
        $this->router = $router;
        $this->security = $security;
        $this->twig = $twig;
        $this->translator = $translator;
    }
    
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $session = $request->getSession();
        $lastUsername = $request->request->get('_username');
        $csrfToken = $request->request->get('_csrf_token');
        
        if (!$this->captchaVerify($request->get('g-recaptcha-response')) && !$this->security->isGranted('ROLE_SUPER_ADMIN')) {
            $session->getFlashBag()->add(
                'error',
                $this->translator->trans('prove.not.robot')    
                
            );           
            
            $content = $this->twig->render(
                '@FOSUser/Security/login.html.twig',
                array(
                    'last_username' => $lastUsername,
                    'error' => null,
                    'csrf_token' => $csrfToken,
                )
            );

            return new Response($content);
        }
        
        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            $response = new RedirectResponse($this->router->generate('admin_dashboard'));
        } else {
            $response = new RedirectResponse($this->router->generate('fos_user_profile_show'));
        }
        
        return $response;
    }
    
    private function captchaVerify($recaptcha)
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
    
}
