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
 *fourtexx
 *
 * @package   com_fourtexx
 * @since     1.0.0
 */
class FourtexxModelDashboard extends ListModel
{
	/**
	 * Method to get a statistic for the dashboard.
	 *
	 * @return  object  Returns itself to support chaining.
	 */
	public function getStatistic()
	{
		$db = JFactory::getDBO();

		$query = $db->getQuery(true);
		$query->select('(SELECT COUNT(*) FROM #__fourtexx_seminars WHERE state=1) as seminars');
		$query->select('(SELECT COUNT(*) FROM #__fourtexx_dates WHERE state=1) as dates');
		$query->select('(SELECT COUNT(*) FROM #__fourtexx_companies WHERE state=1) as companies');
		$query->select('(SELECT COUNT(*) FROM #__fourtexx_registrations WHERE state=1) as registrations');

		$db->setQuery($query);

		$statistic = $db->loadObject();

		return $statistic;
	}
}
