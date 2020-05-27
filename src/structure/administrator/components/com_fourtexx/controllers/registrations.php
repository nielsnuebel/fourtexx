<?php
/**
 * @package    com_fourtexx
 *
 * @author     Niels Nübel <niels@kicktemp.com>
 * @copyright  Kicktemp GmbH
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://kicktemp.com
 */

use Joomla\CMS\MVC\Controller\AdminController;

defined('_JEXEC') or die;

/**
 * FOURTEXX Controller.
 *
 * @package   com_fourtexx
 * @since     1.0.0
 */
class FourtexxControllerRegistrations extends AdminController
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $text_prefix = 'COM_FOURTEXX_REGISTRATIONS';

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JModelLegacy  The model.
	 *
	 * @since   1.0
	 */
	public function getModel($name = 'Registration', $prefix = 'FourtexxModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}
}
