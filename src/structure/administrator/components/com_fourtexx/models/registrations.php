<?php
/**
 * @package    com_fourtexx
 *
 * @author     Niels Nübel <niels@kicktemp.com>
 * @copyright  Kicktemp GmbH
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://kicktemp.com
 */

use Joomla\CMS\MVC\Model\ListModel;

defined('_JEXEC') or die;

/**
 * Fourtexx
 *
 * @package   com_fourtexx
 * @since     1.0.0
 */
class FourtexxModelRegistrations extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     \JModelLegacy
	 * @since   1.0
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'firstname', 'a.firstname',
				'lastname', 'a.lastname',
				'state', 'a.state',
				'note', 'a.note',
				'ordering', 'a.ordering',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'company_id', 'a.company_id',
				'date_id', 'a.date_id',
				'date_title', 'd.title',
				'company_title', 'c.title',
				'created', 'a.created',
				'created_by', 'a.created_by'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function populateState($ordering = 'a.lastname', $direction = 'asc')
	{
		$this->setState('filter.search', $this->getUserStateFromRequest($this->context . '.search', 'filter_search', '', 'string'));
		$this->setState('filter.state', $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '', 'string'));
		$this->setState('filter.date_id', $this->getUserStateFromRequest($this->context . '.filter.date_id', 'filter_date_id', '', 'INT'));
		$this->setState('filter.company_id', $this->getUserStateFromRequest($this->context . '.filter.company_id', 'filter_company_id', '', 'INT'));

		// List state information.
		parent::populateState($ordering, $direction);
	}
	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');

		return parent::getStoreId($id);
	}
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id AS id,' .
				'a.firstname AS firstname,' .
				'a.lastname AS lastname,' .
				'a.checked_out AS checked_out,' .
				'a.checked_out_time AS checked_out_time,' .
				'a.state AS state,' .
				'a.note AS note,' .
				'a.note AS note,' .
				'a.date_id AS date_id,' .
				'a.company_id AS company_id,' .
				'a.ordering AS ordering,' .
				'a.created_by AS created_by'
			)
		);
		$query->from($db->quoteName('#__fourtexx_registrations') . ' AS a');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor')
			->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the users for the author.
		$query->select('ua.name AS author_name')
			->join('LEFT', '#__users AS ua ON ua.id = a.created_by');

		// Join over the users for the checked out user.
		$query->select('c.title AS company_title')
			->join('LEFT', '#__fourtexx_companies AS c ON c.id=a.company_id');

		// Join over the users for the checked out user.
		$query->select('d.title AS date_title')
			->join('LEFT', '#__fourtexx_dates AS d ON d.id=a.date_id');

		// Join over the users for the checked out user.
		$query->select('s.title AS seminar_title')
			->join('LEFT', '#__fourtexx_seminars AS s ON s.id=d.seminar_id');

		// Filter by published state
		$published = $this->getState('filter.state');

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by category.
		$date_id = $this->getState('filter.date_id');

		if (is_numeric($date_id))
		{
			$query->where('a.date_id = ' . (int) $date_id);
		}

		// Filter by category.
		$company_id = $this->getState('filter.company_id');

		if (is_numeric($company_id))
		{
			$query->where('a.company_id = ' . (int) $company_id);
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where('(a.lastname LIKE ' . $search . ' OR a.firstname LIKE ' . $search . ')');
			}
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'a.lastname');
		$orderDirn = $this->state->get('list.direction', 'ASC');

		if ($orderCol == 'a.ordering' || $orderCol == 'date_title' || $orderCol == 'company_title')
		{
			$orderCol = $db->quoteName('d.title') . ' ' . $orderDirn . ', ' . $db->quoteName('c.title') . ' ' . $orderDirn . ', ' . $db->quoteName('a.ordering');
		}

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		$query->group('a.id, 
				a.lastname, 
				a.checked_out, 
				a.checked_out_time, 
				a.state, 
				uc.name,
				ua.name'
		);

		return $query;
	}
}
