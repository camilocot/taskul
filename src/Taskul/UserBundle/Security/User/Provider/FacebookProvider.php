<?php
namespace Taskul\UserBundle\Security\User\Provider;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use \BaseFacebook;
use \FacebookApiException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\DependencyInjection\Container;

class FacebookProvider implements UserProviderInterface
{
    /**
     * @var \Facebook
     */
    protected $facebook;
    protected $userManager;
    protected $validator;
    protected $container;

    public function __construct(BaseFacebook $facebook, $userManager, $validator, Container $container)
    {
        $this->facebook = $facebook;
        $this->userManager = $userManager;
        $this->validator = $validator;
        $this->container = $container;
    }

    public function supportsClass($class)
    {
        return $this->userManager->supportsClass($class);
    }

    public function findUserByFbId($fbId)
    {
        return $this->userManager->findUserBy(array('facebookId' => $fbId));
    }

    public function findUserByFbEmail($fbEmail)
    {
        return $this->userManager->findUserBy(array('email' => $fbEmail));
    }

    public function loadUserByUsername($username)
    {


        try {
            $fbdata = $this->facebook->api('/me');
        } catch (FacebookApiException $e) {
            $fbdata = null;
        }

        if (!empty($fbdata) && isset($fbdata['email'])) {
            $user = $this->findUserByFbEmail($fbdata['email']);
            if (empty($user)) {
                $user = $this->userManager->createUser();
                $user->setEnabled(true);
                // @TODO: esto hay que codificarlo
                $user->setPassword(uniqid());
                $user->setCodeUpload(hash("sha256", uniqid(), false));
            }


            // TODO use http://developers.facebook.com/docs/api/realtime
            $user->setFBData($fbdata);

            if (count($this->validator->validate($user, 'Facebook'))) {
                // TODO: the user was found obviously, but doesnt match our expectations, do something smart
                throw new UsernameNotFoundException('The facebook user could not be stored');
            }

            $this->userManager->updateUser($user);
        }

        if (empty($user)) {

            throw new UsernameNotFoundException('The user is not authenticated on facebook');
        }

        // Asignamos el usuario
        $token = new UsernamePasswordToken($user, null, 'public', array('ROLE_USER'));
        // @TODO no se puede inyectar el security context porque da un error de referencias circulares
        $this->container->get('security.context')->setToken($token);
        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user)) || !$user->getFacebookId()) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getFacebookId());
    }

    public function get($key='/me/friends'){
        try {
            $fbdata = $this->facebook->api($key);
        } catch (FacebookApiException $e) {
            $fbdata = null;

        }
        return $fbdata;
    }

}
