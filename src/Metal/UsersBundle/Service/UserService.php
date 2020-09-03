<?php

namespace Metal\UsersBundle\Service;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;

use Doctrine\ORM\EntityManager;
use Metal\ProjectBundle\Util\RandomGenerator;
use Metal\UsersBundle\Entity\User;

use Metal\UsersBundle\Entity\UserCounter;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UserService extends HelperAbstract
{
    protected $em;

    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    private $usersMailer;

    public function __construct(
        EntityManager $em,
        EncoderFactoryInterface $encoderFactory,
        UserMailer $usersMailer
    ) {
        $this->em = $em;
        $this->encoderFactory = $encoderFactory;
        $this->usersMailer = $usersMailer;
    }

    /**
     * @param array $userData
     *
     * @return User
     */
    public function simpleUserRegister(array $userData)
    {
        $user = new User();
        $user->setEmail($userData['userEmail']);
        $user->setFirstName($userData['userName']);
        $user->setPhone($userData['userPhone']);
        $user->setCity($userData['userCity']);
        $user->setCountry($userData['userCountry']);
        $user->setCompanyTitle($userData['companyTitle']);

        return $this->registerUser($user);
    }

    public function registerUser(User $user)
    {
        $this->updatePassword($user);

        $this->em->persist($user);
        $this->em->flush();

        $this->usersMailer->notifyOnRegistration($user);

        $userCounter = new UserCounter();
        $user->setCounter($userCounter);

        $this->em->persist($userCounter);
        $this->em->flush();

        return $user;
    }

    public function updatePassword(User $user)
    {
        if (!$user->newPassword) {
            $user->newPassword = User::randomPassword();
        }

        $encoder = $this->encoderFactory->getEncoder($user);
        $password = $encoder->encodePassword($user->newPassword, $user->getSalt());
        $user->setPassword($password);
    }

    public function recoveryPassword(User $user)
    {
        if (!$user->getRecoverCode()) {
            $user->setRecoverCode(RandomGenerator::generateRandomCode());
            $this->em->flush();
        }

        $this->usersMailer->sendRecoveryPasswordEmail($user);
    }
}
