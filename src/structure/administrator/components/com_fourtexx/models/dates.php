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
class FourtexxModelDates extends ListModel
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
				'title', 'a.title',
				'alias', 'a.alias',
				'state', 'a.state',
				'note', 'a.note',
				'seats', 'a.seats',
				'ordering', 'a.ordering',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'seminar_id', 'a.seminar_id',
				'seminar_title', 's.title',
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
	protected function populateState($ordering = 'a.title', $direction = 'asc')
	{
		$this->setState('filter.search', $this->getUserStateFromRequest($this->context . '.search', 'filter_search', '', 'string'));
		$this->setState('filter.state', $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '', 'string'));
		$this->setState('filter.seminar_id', $this->getUserStateFromRequest($this->context . '.filter.seminar_id', 'filter_seminar_id', '', 'INT'));

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
				'a.title AS title,' .
				'a.alias AS alias,' .
				'a.checked_out AS checked_out,' .
				'a.checked_out_time AS checked_out_time,' .
				'a.state AS state,' .
				'a.note AS note,' .
				'a.note AS note,' .
				'a.seats AS seats,' .
				'a.seminar_id AS seminar_id,' .
				'a.ordering AS ordering,' .
				'a.created_by AS created_by'
			)
		);
		$query->from($db->quoteName('#__fourtexx_dates') . ' AS a');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor')
			->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the users for the author.
		$query->select('ua.name AS author_name')
			->join('LEFT', '#__users AS ua ON ua.id = a.created_by');

		// Join over the users for the checked out user.
		$query->select('s.title AS seminar_title')
			->join('LEFT', '#__fourtexx_seminars AS s ON s.id=a.seminar_id');

		// Join over the users for the checked out user.
		$query->select('COUNT(r.id) AS registrations')
			->join('LEFT', '#__fourtexx_registrations AS r ON r.date_id=a.id AND r.state=1');

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
		$seminar_id = $this->getState('filter.seminar_id');

		if (is_numeric($seminar_id))
		{
			$query->where('a.seminar_id = ' . (int) $seminar_id);
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
				$query->where('(a.title LIKE ' . $search . ' OR a.alias LIKE ' . $search . ')');
			}
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'a.title');
		$orderDirn = $this->state->get('list.direction', 'ASC');

		if ($orderCol == 'a.ordering' || $orderCol == 'seminar_title')
		{
			$orderCol = $db->quoteName('s.title') . ' ' . $orderDirn . ', ' . $db->quoteName('a.ordering');
		}

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		$query->group('a.id, 
				a.title, 
				a.checked_out, 
				a.checked_out_time, 
				a.state, 
				uc.name,
				ua.name'
		);

		return $query;
	}
}
