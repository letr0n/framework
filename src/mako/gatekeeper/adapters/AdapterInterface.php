<?php

/**
 * @copyright Frederic G. Østby
 * @license   http://www.makoframework.com/license
 */

namespace mako\gatekeeper\adapters;

use mako\gatekeeper\entities\user\UserEntityInterface;
use mako\gatekeeper\repositories\user\UserRepositoryInterface;

/**
 * Adapter interface.
 *
 * @author Frederic G. Østby
 */
interface AdapterInterface
{
	/**
	 * Returns the adapter name.
	 *
	 * @return string
	 */
	public function getName(): string;

	/**
	 * Sets the user repository.
	 *
	 * @param \mako\gatekeeper\repositories\user\UserRepositoryInterface $userRepository User repository
	 */
	public function setUserRepository(UserRepositoryInterface $userRepository);

	/**
	 * Returns the user repository.
	 *
	 * @return \mako\gatekeeper\repositories\user\UserRepositoryInterface
	 */
	public function getUserRepository();

	/**
	 * Sets the active user.
	 *
	 * @param \mako\gatekeeper\entities\user\UserEntityInterface $user User entity
	 */
	public function setUser(UserEntityInterface $user);

	/**
	 * Returns the active user or null if there isn't one.
	 *
	 * @return \mako\gatekeeper\entities\user\UserEntityInterface|null
	 */
	public function getUser();

	/**
	 * Returns true if we don't have an authenticated user and false if we do.
	 *
	 * @return bool
	 */
	public function isGuest(): bool;

	/**
	 * Returns true if we have an authenticated user and false if we don't.
	 *
	 * @return bool
	 */
	public function isLoggedIn(): bool;
}
