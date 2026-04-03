<?php

declare(strict_types=1);

namespace ComponentPHP\Routing\Model;

class SiteMap
{
	public function __construct(
		public array $siteMapEntries,
	) {
	}
}