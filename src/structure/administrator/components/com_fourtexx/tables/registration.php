<?php
/**
 * @package    com_fourtexx
 *
 * @author     Kicktemp GmbH <hello@kicktemp.com>
 * @copyright  Copyright © 2019 Kicktemp GmbH. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://kicktemp.com
 */

use Joomla\CMS\Table\Table;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Access\Rules;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;

defined('_JEXEC') or die;

/**
 * FourtexxTableRegistration table.
 *
 * @package   com_fourtexx
 * @since     1.0.0
 */
class FourtexxTableRegistration extends Table
{
	/**
	 * An array of key names to be json encoded in the bind function
	 *
	 * @var    array
	 * @since  3.3
	 */
	protected $_jsonEncode = array('params', 'rules');

	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database driver object.
	 *
	 * @since   1.0.0
	 */
	public function __construct(\JDatabaseDriver $db)
	{
		parent::__construct('#__fourtexx_registrations', 'id', $db);

		$this->access = (int) \JFactory::getConfig()->get('access');
		// Set the alias since the column is called state
		$this->setColumnAlias('published', 'state');
	}

	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form table_name.id
	 * where id is the value of the primary key of the table.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;

		return 'com_fourtexx.registration.' . (int) $this->$k;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	protected function _getAssetTitle()
	{
		return $this->title;
	}

	/**
	 * Overloaded check function
	 *
	 * @return  boolean  True on success, false on failure
	 *
	 * @see     Table::check()
	 * @since   1.0
	 */
	public function check()
	{
		/**
		 * Ensure any new items have compulsory fields set. This is needed for things like
		 * frontend editing where we don't show all the fields or using some kind of API
		 */
		if (!$this->id)
		{
			// Attributes (article params) can be an empty json string
			if (!isset($this->params))
			{
				$this->params = '{}';
			}


		}

		// Set ordering
		if ($this->state < 0)
		{
			// Set ordering to 0 if state is archived or trashed
			$this->ordering = 0;
		}
		elseif (empty($this->ordering))
		{
			// Set ordering to last if ordering was 0
			$this->ordering = self::getNextOrder($this->_db->quoteName('date_id') . '=' . $this->_db->quote($this->date_id) . ' AND state>=0');
		}

		// CHECK SEATS
		// Verify that the alias is unique
		$table = Table::getInstance('Registration', 'FourtexxTable', array('dbo' => $this->getDbo()));

		if ($table->load(array('id' => $this->id)) && (($table->state != $this->state && $this->state == 1) || $this->id == 0))
		{
			$helper = new FourtexxHelper();

			if (!$helper->freeSeats($this->date_id, 2))
			{
				$this->setError(\JText::_('COM_FOURTEXX_WARNING_NO_SEATS_FREE'));

				return false;
			}
		}

		return true;
	}

	/**
	 * Overrides Table::store to set modified data and user id.
	 *
	 * @param   boolean  $upregistrationNulls  True to upregistration fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.0
	 */
	public function store($upregistrationNulls = false)
	{
		$registration = \JFactory::getDate();
		$user = \JFactory::getUser();

		$this->modified = $registration->toSql();

		if ($this->id)
		{
			// Existing item
			$this->modified_by = $user->get('id');
		}
		else
		{
			// New article. An article created and created_by field can be set by the user,
			// so we don't touch either of these if they are set.
			if (!(int) $this->created)
			{
				$this->created = $registration->toSql();
			}

			if (empty($this->created_by))
			{
				$this->created_by = $user->get('id');
			}
		}

		return parent::store($upregistrationNulls);
	}
}
