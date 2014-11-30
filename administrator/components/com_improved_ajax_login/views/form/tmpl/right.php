<?php
/*-------------------------------------------------------------------------
# com_improved_ajax_login - com_improved_ajax_login
# -------------------------------------------------------------------------
# @ author    Balint Polgarfi
# @ copyright Copyright (C) 2013 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><ul class="nav nav-tabs">
  <li class="active"><a id="form-tab" href="#form-settings" data-toggle="tab"><i class="icon-cog"></i> Form</a></li>
  <li class="hidden"><a id="elem-tab" href="#elem-settings" data-toggle="tab"><i class="icon-wrench"></i> Field</a></li>
</ul>
<div class="tab-content">
  <div class="tab-pane active" id="form-settings">
    <form action="<?php echo JRoute::_('index.php?option=com_improved_ajax_login&layout=edit&id=' .(int)$this->item->id) ?>"
      method="post" enctype="multipart/form-data" name="adminForm" id="form-form" class="form-validate">
      <?php echo $this->form->getInput('id') ?>
      <?php echo $this->form->getInput('created_by') ?>
      <table class="table">
        <thead>
          <tr><th colspan="2">Basic Properties</th></tr>
        </thead>
        <tbody>
          <tr style="display:none">
            <td class="control-label"><?php echo $this->form->getLabel('state') ?></td>
            <td><?php echo $this->form->getInput('state') ?></td>
          </tr>
          <tr>
      			<td class="control-label"><?php echo $this->form->getLabel('title') ?></td>
      			<td><?php echo $this->form->getInput('title') ?></td>
          </tr>
        </tbody>
      </table>
      <?php echo $this->form->getInput('type') ?>
      <?php echo $this->form->getInput('theme') ?>
  		<?php echo $this->form->getInput('props') ?>
  		<?php echo $this->form->getInput('fields') ?>
      <input type="hidden" name="task" value="" />
      <?php echo JHtml::_('form.token'); ?>
    </form>
    <form name="layoutForm" id="layout-form">
      <table class="table">
        <thead>
          <tr><th colspan="2">Layout Properties</th></tr>
        </thead>
        <tbody>
          <tr>
      			<td class="control-label"><?php echo $this->form->getLabel('layout_columns') ?></td>
      			<td><?php echo $this->form->getInput('layout_columns') ?></td>
          </tr>
          <tr>
      			<td class="control-label"><?php echo $this->form->getLabel('layout_margin') ?></td>
      			<td><?php echo $this->form->getInput('layout_margin') ?></td>
          </tr>
          <tr>
      			<td class="control-label"><?php echo $this->form->getLabel('layout_width') ?></td>
      			<td><?php echo $this->form->getInput('layout_width') ?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
  <div class="tab-pane" id="elem-settings">
    <form name="elemForm" id="elem-form">
      <table class="table">
        <thead>
          <tr><th colspan="2">Basic Properties</th></tr>
        </thead>
        <tbody>
          <tr>
      			<td class="control-label"></td>
      			<td>
              <?php echo $this->form->getInput('elem_wide') ?>
              <?php echo $this->form->getLabel('elem_wide') ?>
            </td>
          </tr>
          <tr>
      			<td class="control-label"><?php echo $this->form->getLabel('elem_id') ?></td>
      			<td><?php echo $this->form->getInput('elem_id') ?></td>
          </tr>
          <tr>
      			<td class="control-label"><?php echo $this->form->getLabel('elem_name') ?></td>
      			<td><?php echo $this->form->getInput('elem_name') ?></td>
          </tr>
          <tr>
      			<td class="control-label"></td>
      			<td>
              <?php echo $this->form->getInput('elem_checked') ?>
              <?php echo $this->form->getLabel('elem_checked') ?>
            </td>
          </tr>
          <tr>
      			<td class="control-label"></td>
      			<td>
              <?php echo $this->form->getInput('elem_required') ?>
              <?php echo $this->form->getLabel('elem_required') ?>
            </td>
          </tr>
          <tr>
      			<td class="control-label"><?php echo $this->form->getLabel('elem_label') ?></td>
      			<td><?php echo $this->form->getInput('elem_label') ?></td>
          </tr>
          <tr>
      			<td class="control-label"><?php echo $this->form->getLabel('elem_subtitle') ?></td>
      			<td><?php echo $this->form->getInput('elem_subtitle') ?></td>
          </tr>
          <!--tr>
      			<td class="control-label"><?php echo $this->form->getLabel('elem_icon') ?></td>
      			<td class="field-icon"><?php echo $this->form->getInput('elem_icon') ?></td>
          </tr-->
          <tr>
      			<td class="control-label"><?php echo $this->form->getLabel('elem_placeholder') ?></td>
      			<td><?php echo $this->form->getInput('elem_placeholder') ?></td>
          </tr>
          <tr>
      			<td class="control-label"><?php echo $this->form->getLabel('elem_value') ?></td>
      			<td><?php echo $this->form->getInput('elem_value') ?></td>
          </tr>
          <tr>
      			<td class="control-label"><?php echo $this->form->getLabel('elem_select') ?></td>
      			<td><?php echo $this->form->getInput('elem_select') ?></td>
          </tr>
          <tr>
      			<td class="control-label"><?php echo $this->form->getLabel('elem_title') ?></td>
      			<td><?php echo $this->form->getInput('elem_title') ?></td>
          </tr>
          <tr>
      			<td class="control-label"><?php echo $this->form->getLabel('elem_article') ?></td>
      			<td><?php echo $this->form->getInput('elem_article') ?></td>
          </tr>
          <tr>
      			<td class="control-label"><?php echo $this->form->getLabel('elem_error') ?></td>
      			<td><?php echo $this->form->getInput('elem_error') ?></td>
          </tr>
          <tr>
      			<td class="control-label"><?php echo $this->form->getLabel('elem_pattern') ?></td>
      			<td><?php echo $this->form->getInput('elem_pattern') ?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>