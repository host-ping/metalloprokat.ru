<?php

namespace Brouzie\Bundle\WidgetsBundle\Widget;

use Brouzie\WidgetsBundle\Widget\TwigWidget;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @deprecated Use TwigWidget instead
 */
abstract class WidgetAbstract extends TwigWidget implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var \Symfony\Component\OptionsResolver\OptionsResolver
     */
    protected $optionsResolver;

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $this->optionsResolver = $resolver;
        $this->setDefaultOptions();
    }

    public function setOptions(array $options = array())
    {
        $this->options = $this->optionsResolver->resolve($options);

        return $this;
    }

    public function getContext()
    {
        return $this->getParametersToRender();
    }

    public function render()
    {
        throw new \BadMethodCallException('Method not supported.');
    }

    public function setTemplate($template)
    {
        $this->options['_template'] = $template;

        return $this;
    }

    protected function getParametersToRender()
    {
        return array();
    }

    /**
     * @return Request
     */
    final protected function getRequest()
    {
        return $this->container->get('request_stack')->getMasterRequest();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Session\Session
     */
    protected function getSession()
    {
        return $this->container->get('session');
    }

    /**
     * Checks if the attributes are granted against the current authentication token and optionally supplied object.
     *
     * @param mixed $attributes The attributes
     * @param mixed $object     The object
     *
     * @return bool
     *
     * @throws \LogicException
     */
    protected function isGranted($attributes, $object = null)
    {
        if (!$this->container->has('security.authorization_checker')) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        return $this->container->get('security.authorization_checker')->isGranted($attributes, $object);
    }

    /**
     * Get a user from the Security Token Storage.
     *
     * @return mixed
     *
     * @throws \LogicException If SecurityBundle is not available
     *
     * @see TokenInterface::getUser()
     */
    public function getUser()
    {
        if (!$this->container->has('security.token_storage')) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        if (null === $token = $this->container->get('security.token_storage')->getToken()) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return;
        }

        return $user;
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected function getDoctrine()
    {
        return $this->container->get('doctrine');
    }

    /**
     * Returns a rendered view.
     *
     * @param string $view The view name
     * @param array $parameters An array of parameters to pass to the view
     *
     * @return string The renderer view
     */
    protected function renderView($view, array $parameters = array())
    {
        return $this->container->get('templating')->render($view, $parameters);
    }

    /**
     * Generates a URL from the given parameters.
     *
     * @param string $name The name of the route
     * @param array $parameters An array of parameters
     * @param Boolean $absolute Whether to generate an absolute URL
     *
     * @return string The generated URL
     */
    public function generateUrl($route, array $parameters = array(), $absolute = false)
    {
        return $this->container->get('router')->generate($route, $parameters, $absolute);
    }

    /**
     * Translates the given message.
     *
     * @param string $id The message id
     * @param array $parameters An array of parameters for the message
     * @param string $domain The domain for the message
     *
     * @return string The translated string
     */
    protected function trans($id, array $parameters = array(), $domain = null)
    {
        return $this->container->get('translator')->trans($id, $parameters, $domain);
    }

    /**
     * Translates the given choice message by choosing a translation according to a number.
     *
     * @param string $id The message id
     * @param integer $number The number to use to find the indice of the message
     * @param array $parameters An array of parameters for the message
     * @param string $domain The domain for the message
     *
     * @return string The translated string
     */
    protected function transChoice($id, $number, array $parameters = array(), $domain = null)
    {
        return $this->container->get('translator')->transChoice($id, $number, $parameters, $domain);
    }

    /**
     * Creates and returns a Form instance from the type of the form.
     *
     * @param string|FormTypeInterface $type The built type of the form
     * @param mixed $data The initial data for the form
     * @param array $options Options for the form
     *
     * @return Form
     */
    public function createForm($type, $data = null, array $options = array())
    {
        return $this->container->get('form.factory')->create($type, $data, $options);
    }

    protected function setDefaultOptions()
    {
        $this->optionsResolver->setDefined(array('_template'));
    }
}
