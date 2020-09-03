<?php

namespace Metal\ContentBundle\Controller;

use Metal\UsersBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserContentCategoryController extends Controller
{
    /**
     * @ParamConverter("user", class="MetalUsersBundle:User")
     */
    public function loadUserInfoAction(User $user)
    {
        $this->attachCategoriesToUser($user);

        return $this->render(
            '@MetalContent/UserContent/user_info.html.twig',
            array(
                'user' => $user
            )
        );
    }

    /**
     * @param User $user
     */
    private function attachCategoriesToUser(User $user)
    {
        //TODO: move to repository
        $em = $this->getDoctrine()->getManager();
        $userContentRepository = $em->getRepository('MetalContentBundle:UserContentCategory');

        $userContents = $userContentRepository->findBy(
            array(
                'user' => $user
            )
        );

        $categories = array();
        foreach ($userContents as $userContent) {
            $category = $userContent->getCategory();
            $parent = $category->getParent();
            $partTitle = $parent ? $parent->getTitle() : $category->getTitle();
            $categories[$partTitle][] = $category;
        }

        $user->setAttribute('users_categories', $categories);
    }
}
