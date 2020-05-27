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
 * FourtexxTableCompany table.
 *
 * @package   com_fourtexx
 * @since     1.0.0
 */
class FourtexxTableCompany extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database driver object.
	 *
	 * @since   1.0.0
	 */
	public function __construct(\JDatabaseDriver $db)
	{
		parent::__construct('#__fourtexx_companies', 'id', $db);

		// Set the alias since the column is called state
		$this->setColumnAlias('published', 'state');
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
		if (trim($this->title) == '')
		{
			$this->setError(\JText::_('COM_FOURTEXX_WARNING_PROVIDE_VALID_TITLE'));

			return false;
		}

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

		return true;
	}

	/**
	 * Overrides Table::store to set modified data and user id.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.0
	 */
	public function store($updateNulls = false)
	{
		$date = \JFactory::getDate();
		$user = \JFactory::getUser();

		$this->modified = $date->toSql();

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
				$this->created = $date->toSql();
			}

			if (empty($this->created_by))
			{
				$this->created_by = $user->get('id');
			}
		}

		return parent::store($updateNulls);
	}
}
