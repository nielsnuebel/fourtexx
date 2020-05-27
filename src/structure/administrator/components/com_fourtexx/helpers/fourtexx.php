<?php
/**
 * @package    com_fourtexx
 *
 * @author     Kicktemp GmbH <hello@kicktemp.com>
 * @copyright  Copyright © 2019 Kicktemp GmbH. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://kicktemp.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_fourtexx/models', 'FourtexxModel');

/**
 * FourtexxHelper helper.
 *
 * @package   com_fourtexx
 * @since     1.0.0
 */
class FourtexxHelper
{
	/**
	 * Render submenu.
	 *
	 * @param   string  $vName  The name of the current view.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function addSubmenu($vName)
	{
		HTMLHelper::_('sidebar.addEntry', Text::_('COM_FOURTEXX_SUBMENU_DASHBOARD'), 'index.php?option=com_fourtexx&view=dashboard', $vName === 'dashboard');
		HTMLHelper::_('sidebar.addEntry', Text::_('COM_FOURTEXX_SUBMENU_SEMINARS'), 'index.php?option=com_fourtexx&view=seminars', $vName === 'seminars');
		HTMLHelper::_('sidebar.addEntry', Text::_('COM_FOURTEXX_SUBMENU_DATES'), 'index.php?option=com_fourtexx&view=dates', $vName === 'dates');
		HTMLHelper::_('sidebar.addEntry', Text::_('COM_FOURTEXX_SUBMENU_COMPANIES'), 'index.php?option=com_fourtexx&view=companies', $vName === 'companies');
		HTMLHelper::_('sidebar.addEntry', Text::_('COM_FOURTEXX_SUBMENU_REGISTRATIONS'), 'index.php?option=com_fourtexx&view=registrations', $vName === 'registrations');
	}

	public function freeSeats($date_id, $seats = 0)
	{
		// Get an instance of the generic articles model
		$model = JModelLegacy::getInstance('Dates', 'FourtexxModel', array('ignore_request' => true));
		$model->setState('filter.search', 'id:' . $date_id);

		$items = $model->getItems();

		if ((int) $items[0]->seats <= (int) $items[0]->registrations + $seats)
		{
			return false;
		}

		return true;
	}
}
