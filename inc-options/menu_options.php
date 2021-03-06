<?php
/**
 * @package Adminimize
 * @subpackage Menu, Submenu Options
 * @author Frank Bültge
 */
if ( ! function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a part of plugin, not much I can do when called directly.";
	exit;
}
?>

		<div id="poststuff" class="ui-sortable meta-box-sortables">
			<div class="postbox">
				<div class="handlediv" title="<?php _e('Click to toggle'); ?>"><br/></div>
				<h3 class="hndle" id="config_menu"><?php _e('Menu Options', FB_ADMINIMIZE_TEXTDOMAIN ); ?></h3>
				<div class="inside">
					<br class="clear" />
					
					<table summary="config_menu" class="widefat">
						<thead>
							<tr>
								<th><?php _e('Menu options - Menu, <span>Submenu</span>', FB_ADMINIMIZE_TEXTDOMAIN ); ?></th>

								<?php foreach ($user_roles_names as $role_name) { ?>
										<th><?php _e('Deactivate for', FB_ADMINIMIZE_TEXTDOMAIN ); echo '<br/>' . $role_name; ?></th>
								<?php } ?>

							</tr>
						</thead>
						<tbody>
							<?php
							$wp_menu    = _mw_adminimize_get_option_value('mw_adminimize_default_menu');
							$wp_submenu = _mw_adminimize_get_option_value('mw_adminimize_default_submenu');

							// Object to array
							if ( is_object( $wp_submenu ) )
								$wp_submenu = get_object_vars( $wp_submenu );

							if ( ! isset($wp_menu) || empty($wp_menu) ) {
								global $menu;
								
								$wp_menu = $menu;
							}
							if ( ! isset($wp_submenu) || empty($wp_submenu) ) {
								global $submenu;
								
								$wp_submenu = $submenu;
							}

							foreach ($user_roles as $role) {
								$disabled_metaboxes_post_[$role]  = _mw_adminimize_get_option_value('mw_adminimize_disabled_metaboxes_post_'. $role .'_items');
								$disabled_metaboxes_page_[$role]  = _mw_adminimize_get_option_value('mw_adminimize_disabled_metaboxes_page_'. $role .'_items');
							}
							
							// print menu, submenu
							if ( isset( $wp_menu ) && '' !== $wp_menu ) {
								
								$i = 0;
								$x = 0;
								$class = '';
								
								$users = array(
										0 => 'Profile',
										1 => 'edit_users',
										2 => 'profile.php',
										3 => '',
										4 => 'menu-top',
										5 => 'menu-users',
										6 => 'div'
								);
								
								foreach ( $wp_menu as $key => $item ) {

									// non checked items
									if ( $item[2] === 'options-general.php' ) {
										//$disabled_item_adm = ' disabled="disabled"';
										$disabled_item_adm_hint = '<abbr title="' . __( 'After activate the check box it heavy attitudes will change.', FB_ADMINIMIZE_TEXTDOMAIN ) . '" style="cursor:pointer;"> ! </acronym>';
									} else {
										$disabled_item_adm = '';
										$disabled_item_adm_hint = '';
									}

									if ( '' !== $item[2] ) {
										
										if ( 'wp-menu-separator' === $item[4] ) {
											$item[ 0 ] = 'Separator';
										}

										foreach ( $user_roles as $role ) {

											// checkbox checked
											$checked_user_role_[$role] = '';
											if ( isset( $disabled_menu_[$role] )
													// @since 2015-11-11
													// Switch to the key and url of menu item.
													&& _mw_adminimize_in_arrays( array( $key, $item[2] ), $disabled_menu_[$role] )
											) {
												$checked_user_role_[$role] = ' checked="checked"';
											}
										}

										echo '<tr class="form-invalid">' . "\n";
										echo "\t" . '<th>' . $item[ 0 ] . ' <span>['
												. $key . ']('
												. preg_replace(
														"#[%2].*#",
														'...',
														htmlentities( $item[ 2 ] )
												) . ')</span> </th>';

										foreach ( $user_roles as $role ) {
											if ( $role !== 'administrator' ) { // only admin disable items
												$disabled_item_adm = '';
												$disabled_item_adm_hint = '';
											}
											echo "\t" . '<td class="num">' . $disabled_item_adm_hint . '<input id="check_menu'
													. $role . $x .'" type="checkbox"' . $disabled_item_adm . $checked_user_role_[$role]
													. ' name="mw_adminimize_disabled_menu_'. $role .'_items[]" value="'
													. htmlentities( $item[2] ) . '" />' . $disabled_item_adm_hint . '</td>' . "\n";
										}
										echo '</tr>';

										// Only for user smaller administrator, change user-Profile-File.
										if ( 'users.php' === $item[2] ) {
											$x++;
											echo '<tr class="form-invalid">' . "\n";
											echo "\t" . '<th>' . __('Profile') . ' <span>(profile.php)</span> </th>';
											foreach ( $user_roles as $role ) {
												echo "\t" . '<td class="num"><input disabled="disabled" id="check_menu' . $role . $x
														. '" type="checkbox"' . $checked_user_role_[$role]
														. ' name="mw_adminimize_disabled_menu_' . $role
														. '_items[]" value="profile.php" /></td>' . "\n";
											}
											echo '</tr>';
										}

										$x++;

										if ( ! isset( $wp_submenu[ $item[ 2 ] ] ) ) {
											continue;
										}

										// Loop about submenu items.
										foreach ( $wp_submenu[ $item[2] ] as $subkey => $subitem ) {

											$class = ( ' class="alternate"' === $class ) ? '' : ' class="alternate"';

											// Special solutions for the Adminimize link, that it not works on settings site.
											if ( $subitem[2] === 'adminimize/adminimize.php' ) {
												//$disabled_subitem_adm = ' disabled="disabled"';
												$disabled_subitem_adm_hint = '<abbr title="'
														. __( 'After activate the check box it heavy attitudes will change.', FB_ADMINIMIZE_TEXTDOMAIN )
														. '" style="cursor:pointer;"> ! </acronym>';
											} else {
												$disabled_subitem_adm = '';
												$disabled_subitem_adm_hint = '';
											}
											
											echo '<tr' . $class . '>' . "\n";
											foreach ($user_roles as $role) {
												// checkbox checked
												$checked_user_role_[$role] = '';
												if ( isset( $disabled_submenu_[$role] )
														// @since 2015-11-11
														// Switch to custom key and url of menu item.
														&& _mw_adminimize_in_arrays( array( $item[2] . '__' . $subkey, $subitem[2] ), $disabled_submenu_[$role] )
												) {
													$checked_user_role_[$role] = ' checked="checked"';
												}
											}
											echo '<td> &mdash; ' . $subitem[0] . ' <span>['
													.  $subkey . ']('
													. preg_replace(
															"#[%2].*#",
															'...',
															htmlentities( $subitem[2] )
													) . ')</span> </td>' . "\n";

											foreach ($user_roles as $role) {
												if ( $role !== 'administrator' ) { // only admin disable items
													$disabled_subitem_adm = '';
													$disabled_subitem_adm_hint = '';
												}
												echo '<td class="num">' . $disabled_subitem_adm_hint . '<input id="check_menu' . $role . $x
														. '" type="checkbox"' . $disabled_subitem_adm . $checked_user_role_[$role]
														. ' name="mw_adminimize_disabled_submenu_'. $role .'_items[]" value="'
														. $item[2] . '__' . $subkey . '" />' . $disabled_subitem_adm_hint . '</td>' . "\n";
											}
											echo '</tr>' . "\n";
											$x++;
										}
										$i++;
										$x++;
									}
								}

							} else {
								$myErrors = new _mw_adminimize_message_class();
								$myErrors = '<tr><td style="color: red;">' . $myErrors->get_error('_mw_adminimize_get_option') . '</td></tr>';
								echo $myErrors;
							} ?>
						</tbody>
					</table>
					
					<p id="submitbutton">
						<input class="button button-primary" type="submit" name="_mw_adminimize_save" value="<?php _e('Update Options', FB_ADMINIMIZE_TEXTDOMAIN ); ?> &raquo;" /><input type="hidden" name="page_options" value="'dofollow_timeout'" />
					</p>
					<p><a class="alignright button" href="javascript:void(0);" onclick="window.scrollTo(0,0);" style="margin:3px 0 0 30px;"><?php _e('scroll to top', FB_ADMINIMIZE_TEXTDOMAIN); ?></a><br class="clear" /></p>

				</div>
			</div>
		</div>
		