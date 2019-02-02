<?php
namespace macsch15\capsule\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\template\template */
	protected $template;

	/**
	* Constructor
	*
	* @param \phpbb\controller\helper $helper
	* @param \phpbb\template\template $template
	* @return void
	*/
	public function __construct(
		\phpbb\controller\helper $helper, 
		\phpbb\template\template $template
	) {
		$this->helper = $helper;
		$this->template = $template;
	}

	/**
	* Assign functions defined in this class to event listeners in the core
	*
	* @return array
	*/
	static public function getSubscribedEvents()
	{
		return [
			'core.user_setup' => 'loadLanguageOnSetup',
			'core.page_header' => 'addPageHeaderLink'
		];
	}

	/**
	 * @param array $event
	 * @return void
	 */
	public function loadLanguageOnSetup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];

		$lang_set_ext[] = [
			'ext_name' => 'macsch15/capsule',
			'lang_set' => 'capsule'
		];

		$event['lang_set_ext'] = $lang_set_ext;
	}

	/**
	 * @return void
	 */
	public function addPageHeaderLink()
	{
		$this->template->assign_vars([
			'CAPSULE_URL' => $this->helper->route('macsch15_capsule_main_controller')
		]);
	}
}
