<?php

namespace Arts\QueryControl\Containers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Arts\Base\Containers\ManagersContainer as BaseManagersContainer;
use Arts\QueryControl\Managers\Controls;
use Arts\QueryControl\Managers\Compatibility;

/**
 * Managers Container
 *
 * Type-safe container for plugin managers.
 *
 * @since 1.0.0
 *
 * @property Controls $controls Controls manager instance.
 * @property Compatibility $compatibility Compatibility manager instance.
 */
class ManagersContainer extends BaseManagersContainer {
	public Controls $controls;
	public Compatibility $compatibility;
}
