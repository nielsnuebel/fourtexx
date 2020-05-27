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
class FourtexxViewSeminar extends HtmlView
{
	/**
	 * The JForm object
	 *
	 * @var  JForm
	 */
	protected $form;

	/**
	 * The active item
	 *
	 * @var  object
	 */
	protected $item;

	/**
	 * The model state
	 *
	 * @var  object
	 */
	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 */
	public function display($tpl = null)
	{
		$this->form     = $this->get('Form');
		$this->item     = $this->get('Item');
		$this->state    = $this->get('State');
		$this->canDo	= JHelperContent::getActions('com_fourtexx');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

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
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= $this->canDo;

		JToolbarHelper::title($isNew ? JText::_('COM_FOURTEXX_MANAGER_SEMINAR_NEW') :
			JText::_('COM_FOURTEXX_MANAGER_SEMINAR_EDIT'), 'pencil-2');
		// If not checked out, can save the item.

		if (!$checkedOut && ($canDo->get('core.edit') || $canDo->get('core.create')))
		{
			JToolbarHelper::apply('seminar.apply');
			JToolbarHelper::save('seminar.save');
		}

		if (!$checkedOut && $canDo->get('core.create'))
		{
			JToolbarHelper::save2new('seminar.save2new');
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create'))
		{
			JToolbarHelper::save2copy('seminar.save2copy');
		}

		if (empty($this->item->id))
		{
			JToolbarHelper::cancel('seminar.cancel');
		}
		else
		{
			if ($this->state->params->get('save_history', 0) && $user->authorise('core.edit'))
			{
				JToolbarHelper::versions('com_fourtexx.seminar', $this->item->id);
			}
			JToolbarHelper::cancel('seminar.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
