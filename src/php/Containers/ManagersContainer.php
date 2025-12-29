<?php

namespace Arts\QueryControl\Containers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Arts\Base\Containers\ManagersContainer as BaseManagersContainer;
use Arts\QueryControl\Managers\Controls;
use Arts\QueryControl\Managers\Compatibility;

/**
 * @property Controls $controls
 * @property Compatibility $compatibility
 */
class ManagersContainer extends BaseManagersContainer {
	// Empty - uses parent's ArrayObject storage with type safety via @property annotations
}
