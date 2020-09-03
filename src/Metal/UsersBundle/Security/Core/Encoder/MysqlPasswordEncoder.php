<?php

namespace Metal\UsersBundle\Security\Core\Encoder;

use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;

class MysqlPasswordEncoder extends BasePasswordEncoder
{
	/**
	 * {@inheritdoc}
	 */
	public function encodePassword($raw, $salt)
	{
		return '*' . strtoupper(sha1(sha1($raw, true)));
	}

	/**
	 * {@inheritdoc}
	 */
	public function isPasswordValid($encoded, $raw, $salt)
	{
		return $this->comparePasswords($encoded, $this->encodePassword($raw, $salt));
	}
}
