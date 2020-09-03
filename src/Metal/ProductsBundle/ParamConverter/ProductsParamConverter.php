<?php

namespace Metal\ProductsBundle\ParamConverter;

use Doctrine\ORM\EntityManager;
use Metal\ProductsBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ProductsParamConverter implements ParamConverterInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em, TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;

        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $param = $configuration->getName();

        $id = $request->attributes->get('id');
        $productRepository = $this->em->getRepository('MetalProductsBundle:Product');
        $product = $productRepository->createQueryBuilder('p')
            ->join('p.category', 'c')
            ->addSelect('c')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
        /* @var $product Product */

        if (!$product) {
            throw new NotFoundHttpException('Product not found.');
        }

        $user = null;
        if ($this->authorizationChecker->isGranted('ROLE_USER')) {
            $user = $this->tokenStorage->getToken()->getUser();
        }

        $productRepository->attachFavoriteToProducts(array($product), $user);
        
        $this->em->getRepository('MetalProductsBundle:ProductAttributeValue')->attachAttributesCollectionToProducts(array($product));

        $request->attributes->set($param, $product);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        if (null === $configuration->getClass()) {
            return false;
        }

        return Product::class === $configuration->getClass();
    }
}
