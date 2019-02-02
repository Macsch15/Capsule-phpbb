<?php
namespace macsch15\capsule;

class ext extends \phpbb\extension\base
{
	/**
	* Check whether or not the extension can be enabled.
	* The current phpBB version should meet or exceed
	* the minimum version required by this extension:
	*
	* Requires phpBB 3.1.3 due to usage of container aware migrations.
	*
	* @return bool
	*/
	public function is_enableable()
	{
		$config = $this->container->get('config');

		return phpbb_version_compare($config['version'], '3.1.3', '>=');
	}
}
