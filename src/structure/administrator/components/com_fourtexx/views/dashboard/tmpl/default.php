<?php
/**
 * @package    KickGDPR
 *
 * @author     Niels Nübel <niels@kicktemp.com>
 * @author     Stefan Wendhausen <stefan@kicktemp.com>
 * @copyright  Copyright © 2018-2019 Kicktemp GmbH. All Rights Reserved
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.htm
 * @link       https://kicktemp.com
 */
?>
<div class="row-fluid">
	<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<?php else : ?>
		<div id="j-main-container">
			<?php endif;?>
			<div class="row-fluid">
				<div class="span8">
					<?php echo JText::_('COM_FOURTEXX_COMP');?>
					<?php echo JText::_('COM_FOURTEXX_VERSION');?>
					<?php echo JText::_('COM_FOURTEXX_COMP_DESC');?>
					<?php echo JText::_('COM_FOURTEXX_INFO');?>
					<?php echo JText::_('COM_FOURTEXX_COPYRIGHT') . ' | ' . JText::_('COM_FOURTEXX_LICENSE'); ?>
				</div>
				<div class="span4 well alert-success">
					<h3><?php echo JText::_('COM_FOURTEXX_CPANEL_STATISTIC');?></h3>
					<table class="table table-condensed table-hover">
						<thead>
						<tr>
							<th><?php echo JText::_('COM_FOURTEXX_CPANEL_STATISTICTABLE');?></th>
							<th><?php echo JText::_('COM_FOURTEXX_CPANEL_COUNT');?></th>
						</tr>
						</thead>
						<tbody>
                        <tr>
                            <td><?php echo JText::_('COM_FOURTEXX_CPANEL_SEMINARS'); ?></td>
                            <td><?php echo $this->statistic->seminars;?></td>
                        </tr>
                        <tr>
                            <td><?php echo JText::_('COM_FOURTEXX_CPANEL_DATES'); ?></td>
                            <td><?php echo $this->statistic->dates;?></td>
                        </tr>
                        <tr>
                            <td><?php echo JText::_('COM_FOURTEXX_CPANEL_COMPANIES'); ?></td>
                            <td><?php echo $this->statistic->companies;?></td>
                        </tr>
                        <tr>
                            <td><?php echo JText::_('COM_FOURTEXX_CPANEL_REGISTRATIONS'); ?></td>
                            <td><?php echo $this->statistic->registrations;?></td>
                        </tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
