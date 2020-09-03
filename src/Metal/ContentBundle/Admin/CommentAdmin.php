<?php

namespace Metal\ContentBundle\Admin;

use Metal\ContentBundle\Entity\Comment;
use Metal\ContentBundle\Entity\Question;
use Metal\ContentBundle\Entity\ValueObject\StatusTypeProvider;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class CommentAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'id',
    );

    public function configure()
    {
        $this->classnameLabel = 'Content Comment';
    }

    public function toString($object)
    {
        return $object instanceof Comment ? sprintf('Комментарий %d', $object->getId()) : '';
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('delete');
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $subject = $this->getSubject();
        /* @var $subject Comment */

        $entryName = '';
        if ($subject instanceof Comment) {
            $entryName = 'Топика';
            if ($subject->getContentEntry() instanceof Question) {
                $entryName = 'Вопроса';
            }
        }

        if ($entryName) {
            $formMapper
                ->add(
                    'contentEntry',
                    'entity_id',
                    array(
                        'label' => 'ID '.$entryName,
                        'class' => 'MetalContentBundle:AbstractContentEntry',
                        'required' => false,
                        'hidden' => false,
                    )
                );
        } else {
            $formMapper
                ->add(
                    'instagramPhoto',
                    'entity_id',
                    array(
                        'label' => 'ID Фото',
                        'class' => 'MetalContentBundle:InstagramPhoto',
                        'required' => false,
                        'hidden' => false,
                    )
                );
        }

        $formMapper
            ->add(
                'parent',
                'entity_id',
                array(
                    'label' => 'ID Родителя',
                    'class' => $entryName ? 'MetalContentBundle:Comment' : 'MetalContentBundle:InstagramComment',
                    'required' => false,
                    'hidden' => false,
                )
            )
            ->add(
                'user',
                'entity_id',
                array(
                    'label' => 'ID Пользователя',
                    'class' => 'MetalUsersBundle:User',
                    'required' => false,
                    'hidden' => false,
                )
            )
            ->add(
                'statusTypeId',
                'choice',
                array(
                    'label' => 'Статус модерации',
                    'choices' => StatusTypeProvider::getAllTypesAsSimpleArray(),
                )
            )
            ->add(
                'description',
                null,
                array(
                    'label' => 'Комментарий',
                    'attr' => array(
                        'data' => 'editable-with-bbcode',
                    ),
                )
            );

        if (!$subject->getUser()) {
            $formMapper
                ->add(
                    'email',
                    null,
                    array(
                        'label' => 'Email комментатора',
                        'required' => false
                    )
                )
                ->add(
                    'name',
                    null,
                    array(
                        'label' => 'Имя комментатора',
                        'required' => false
                    )
                )
            ;
        }
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add(
                'user',
                null,
                array(
                    'label' => 'Пользователь',
                    'template' => 'MetalContentBundle:Admin:user.html.twig',
                )
            )
            ->add(
                'contentEntry',
                null,
                array(
                    'label' => 'Тема/Вопрос',
                    'template' => 'MetalContentBundle:Admin:content_entry.html.twig',
                )
            )
            ->add('createdAt', null, array('label' => 'Дата добавления'))
            ->add(
                'statusTypeId',
                'choice',
                array(
                    'label' => 'Статус модерации',
                    'choices' => StatusTypeProvider::getAllTypesAsSimpleArray(),
                )
            )
            ->add('description', null, array('label' => 'Комментарий', 'template' => 'MetalContentBundle:Admin:description.html.twig',))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'description',
                null,
                array(
                    'label' => 'Комментарий',
                )
            )
            ->add(
                'statusTypeId',
                'doctrine_orm_choice',
                array('label' => 'Статус модерации'),
                'choice',
                array(
                    'choices' => StatusTypeProvider::getAllTypesAsSimpleArray(),
                )
            );
    }
}
