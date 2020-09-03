<?php

namespace Metal\ContentBundle\Form;

use Metal\ContentBundle\Entity\Comment;
use Metal\UsersBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CommentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $noVisibleCaptchaEmails = array(
            'yawa3891@ukr.net',
            'yawa3891@mail.ru',
            'meblionline.ru@yandex.ru',
            'rusdrevo24.ru@yandex.ru',
            'prodveristroy.ru@yandex.ru',
            'mydverionline.ru@yandex.ru',
            'stolzrnaya.ru@yandex.ru',
        );

        $visibleCaptcha = true;
        if ($options['user'] instanceof User && in_array($options['user']->getEmail(), $noVisibleCaptchaEmails)) {
            $visibleCaptcha = false;
        }

        if (!$options['is_authenticated']) {
            $builder
                ->add('name')
                ->add('email');
        }

        $builder
            ->add('description', 'textarea')
            ->add(
                'parent',
                'entity_id',
                array(
                    'class' => $options['data_class'],
                )
            );

        if ($visibleCaptcha) {
            $builder->add(
                'captcha',
                'captcha',
                array(
                    'reload' => true,
                    'as_url' => true,
                    'invalid_message' => 'Неверный код с картинки',
                )
            );
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('is_authenticated', 'user'));

        $resolver->setDefaults(
            array(
                'data_class' => Comment::class,
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_content_comment';
    }
}
