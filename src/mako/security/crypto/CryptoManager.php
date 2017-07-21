<?php

/**
 * @copyright Frederic G. Østby
 * @license   http://www.makoframework.com/license
 */

namespace mako\security\crypto;

use RuntimeException;

use mako\common\AdapterManager;
use mako\security\crypto\Crypto;
use mako\security\crypto\encrypters\OpenSSL;
use mako\security\Key;

/**
 * Crypto manager.
 *
 * @author Frederic G. Østby
 *
 * @method \mako\security\crypto\encrypters\EncrypterInterface instance(string $configuration = null)
 * @method string                                              encrypt(string $string)
 * @method string|bool                                         decrypt(string $string)
 */
class CryptoManager extends AdapterManager
{
	/**
	 * OpenSSL encrypter factory.
	 *
	 * @access protected
	 * @param  array                                    $configuration Configuration
	 * @return \mako\security\crypto\encrypters\OpenSSL
	 */
	protected function opensslFactory(array $configuration): OpenSSL
	{
		return new OpenSSL(Key::decode($configuration['key']), $configuration['cipher']);
	}

	/**
	 * Returns a crypto instance.
	 *
	 * @access public
	 * @param  string                       $configuration Configuration name
	 * @return \mako\security\crypto\Crypto
	 */
	protected function instantiate(string $configuration): Crypto
	{
		if(!isset($this->configurations[$configuration]))
		{
			throw new RuntimeException(vsprintf("%s(): [ %s ] has not been defined in the crypto configuration.", [__METHOD__, $configuration]));
		}

		$configuration = $this->configurations[$configuration];

		return new Crypto($this->factory($configuration['library'], $configuration), $this->container->get('signer'));
	}
}
