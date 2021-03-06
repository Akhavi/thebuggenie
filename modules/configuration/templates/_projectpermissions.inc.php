<div class="content">
	<p><?php echo __('These permissions control what you can do, and which pages you can access in The Bug Genie - on a project-specific basis. Some of these permissions are also available as site-wide permissions in the %permissions_configuration% page.', array('%permissions_configuration%' => '<b>'.link_tag(make_url('configure_permissions'), __('permissions configuration')).'</b>')); ?></p>
</div>
<div class="permission_list">
	<ul>
		<li>
			<a href="javascript:void(0);" onclick="$('project_permission_details_<?php echo $project->getID(); ?>').toggle();"><?php echo image_tag('icon_project_permissions.png', array('style' => 'float: right;')); ?><?php echo $project->getName(); ?> <span class="faded_out smaller"><?php echo $project->getKey(); ?></span></a>
			<ul style="display: none;" id="project_permission_details_<?php echo $project->getID(); ?>">
				<?php include_template('configuration/permissionsblock', array('base_id' => 0 . 'project_' . $project->getID() . '_project_permissions', 'permissions_list' => TBGContext::getAvailablePermissions('project'), 'mode' => 'general', 'target_id' => $project->getID(), 'module' => 'core', 'access_level' => $access_level)); ?>
				<?php include_template('configuration/permissionsblock', array('base_id' => 0 . 'project_' . $project->getID() . '_page_permissions', 'permissions_list' => TBGContext::getAvailablePermissions('project_pages'), 'mode' => 'project_pages', 'target_id' => $project->getID(), 'module' => 'core', 'access_level' => $access_level)); ?>
				<?php include_template('configuration/permissionsblock', array('base_id' => 0 . 'project_' . $project->getID() . '_issue_permissions', 'permissions_list' => TBGContext::getAvailablePermissions('issues'), 'mode' => 'general', 'target_id' => $project->getID(), 'module' => 'core', 'access_level' => $access_level)); ?>
			</ul>
		</li>
	</ul>
</div>