<?php
/**
 * @package    com_fourtexx
 *
 * @author     Niels Nübel <niels@kicktemp.com>
 * @copyright  Kicktemp GmbH
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://kicktemp.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

defined('_JEXEC') or die;

/**
 * View class for a list of dashboard.
 *
 * @package  KickGDPR
 *
 * @since    1.6
 */
class FourtexxViewDashboard extends HtmlView
{
	/**
	 * The Result in a Object from Database
	 *
	 * @var    object
	 */
	protected $statistic;

	/**
	 *fourtexx helper
	 *
	 * @var   fourtexxHelper
	 * @since  1.0.0
	 */
	protected $helper;

	/**
	 * The sidebar to show
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $sidebar = '';

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 */
	public function display($tpl = null)
	{
		$this->statistic = $this->get('Statistic');

		// Show the sidebar
		$this->helper = new FourtexxHelper;
		$this->helper->addSubmenu('dashboard');
		$this->sidebar = HTMLHelper::_('sidebar.render');

		// Show the toolbar
		$this->toolbar();

		parent::display($tpl);
	}

	/**
	 * Displays a toolbar for a specific page.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	private function toolbar()
	{
		ToolBarHelper::title(Text::_( 'COM_FOURTEXX'). ': ' . Text::_( 'COM_FOURTEXX_DASHBOARD'), 'dashboard');

		// Options button.
		if (Factory::getUser()->authorise('core.admin', 'com_fourtexx'))
		{
			ToolBarHelper::preferences('com_fourtexx');
		}
	}
}
