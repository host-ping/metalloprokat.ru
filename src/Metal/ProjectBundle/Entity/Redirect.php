<?php

namespace Metal\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\ProjectBundle\Entity\Behavior\Updateable;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="redirect")
 */
class Redirect
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\Column(length=255, name="redirect_from")
     *
     * @Assert\Length(max="255")
     * @Assert\NotBlank()
     */
    protected $redirectFrom;

    /**
     * @ORM\Column(length=255, name="redirect_to")
     *
     * @Assert\Length(max="255")
     * @Assert\NotBlank()
     */
    protected $redirectTo;

    /**
     * @ORM\Column(length=255, name="example_url")
     *
     * @Assert\Length(max="255")
     * @Assert\NotBlank()
     */
    protected $exampleUrl;

    /**
     * @ORM\Column(type="boolean", name="enabled", nullable=false, options={"default":1})
     */
    protected $enabled;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     *
     * @var \DateTime
     */
    protected $createdAt;

    use Updateable;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->enabled = true;
        $this->exampleUrl = '';
    }

    public function getId()
    {
        return $this->id;
    }

    public function getRedirectFrom()
    {
        return $this->redirectFrom;
    }

    public function setRedirectFrom($redirectFrom)
    {
        $this->redirectFrom = (string)$redirectFrom;
    }

    public function getRedirectTo()
    {
        return $this->redirectTo;
    }

    public function setRedirectTo($redirectTo)
    {
        $this->redirectTo = (string)$redirectTo;
    }

    public function getExampleUrl()
    {
        return $this->exampleUrl;
    }

    public function setExampleUrl($exampleUrl)
    {
        $this->exampleUrl = (string)$exampleUrl;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @Assert\Callback()
     */
    public function isPatternValid(ExecutionContextInterface $context)
    {
        $message = null;
        $result = null;
        try {
            $result = preg_replace($this->getRedirectFrom(), $this->getRedirectTo(), $this->getExampleUrl());
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
        }

        if ($message || preg_last_error() !== PREG_NO_ERROR) {
            $context
                ->buildViolation($message ?: 'Неверно заполнены шаблоны для перенаправления')
                ->atPath('redirectFrom')
                ->atPath('redirectTo')
                ->addViolation()
            ;
        }

        if ($result === $this->getExampleUrl()) {
            $context
                ->buildViolation('Адрес страницы не изменился, шаблоны не верны.')
                ->atPath('exampleUrl')
                ->addViolation()
            ;
        }
    }
}
