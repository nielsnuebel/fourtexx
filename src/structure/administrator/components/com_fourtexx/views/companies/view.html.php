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
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

defined('_JEXEC') or die;

/**
 * View class for a list of dashboard.
 *
 * @package  Fourtexx
 *
 * @since    1.6
 */
class FourtexxViewCompanies extends HtmlView
{
	/**
	 * Fourtexx helper
	 *
	 * @var    Fourtexx
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
	 * An array of items
	 *
	 * @var  array
	 */
	protected $items;

	/**
	 * The pagination object
	 *
	 * @var  JPagination
	 */
	protected $pagination;

	/**
	 * The model state
	 *
	 * @var  object
	 */
	protected $state;

	/**
	 * Form object for search filters
	 *
	 * @var  JForm
	 */
	public $filterForm;

	/**
	 * The active search filters
	 *
	 * @var  array
	 */
	public $activeFilters;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 */
	public function display($tpl = null)
	{
		$this->state         = $this->get('State');
		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// Show the sidebar
		$this->helper = new FourtexxHelper;
		$this->helper->addSubmenu('companies');
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
		$canDo = ContentHelper::getActions('com_fourtexx');
		ToolBarHelper::title(Text::_( 'COM_FOURTEXX'). ': ' . Text::_( 'COM_FOURTEXX_COMPANIES'), 'home');

		// Options button.
		if (Factory::getUser()->authorise('core.admin', 'com_fourtexx'))
		{
			ToolBarHelper::preferences('com_fourtexx');
		}

		if ($canDo->get('core.create'))
		{
			ToolBarHelper::addNew('company.add');
		}

		if ($canDo->get('core.edit'))
		{
			ToolBarHelper::editList('company.edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			ToolbarHelper::publish('companies.publish', 'JTOOLBAR_PUBLISH', true);
			ToolbarHelper::unpublish('companies.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			ToolbarHelper::archiveList('companies.archive');
		}

		if (Factory::getUser()->authorise('core.admin'))
		{
			ToolbarHelper::checkin('companies.checkin');
		}

		if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete'))
		{
			ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'Companies.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			ToolbarHelper::trash('companies.trash');
		}
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'a.ordering'    => Text::_('JGRID_HEADING_ORDERING'),
			'a.state'       => Text::_('JSTATUS'),
			'a.title'       => Text::_('JGLOBAL_TITLE'),
			'a.id'          => Text::_('JGRID_HEADING_ID'),
		);
	}
}
