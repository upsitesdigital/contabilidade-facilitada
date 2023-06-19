<?php
if (!defined('ABSPATH')) {
    exit('Direct\'s not allowed');
}

//add js, css
add_action('admin_enqueue_scripts', 'cf7_custom_wp_admin_style');
function cf7_custom_wp_admin_style()
{
    wp_register_script('cf7d_js', CF7D_PLUGIN_URL . '/admin/assets/js/cf7d.js', array('jquery'));

    wp_register_style('cf7d', CF7D_PLUGIN_URL . '/admin/assets/css/cf7d.css');
    wp_enqueue_style('cf7d');
}

//add admin menu page
add_action('admin_menu', 'cf7d_register_custom_submenu_page');
function cf7d_register_custom_submenu_page()
{
    $menu = add_submenu_page('wpcf7', 'Database', 'Database', 'manage_options', 'cf7-data', 'cf7d_custom_submenu_page_callback');
    add_action('load-' . $menu, 'cf7d_form_action_callback');
}
function cf7d_custom_submenu_page_callback_bak()
{
    if (!class_exists('WP_List_Table')) {
        require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
    }
    require_once 'table.php';
    $entries = new Cf7_Entries_List();
    ?>
    <div class="wrap">
        <h2>WP_List_Table Class Example</h2>

        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">
                    <div class="meta-box-sortables ui-sortable">
                        <form method="post">
                            <?php
                            $entries->prepare_items();
                            $entries->display(); ?>
                        </form>
                    </div>
                </div>
            </div>
            <br class="clear">
        </div>
    </div>
    <?php
}
function cf7d_custom_submenu_page_callback()
{
    global $wpdb;

    wp_enqueue_script('cf7d_js');
    wp_enqueue_script('jquery-ui-sortable');
    echo '<div class="wrap"><h1>' . __('Data') . '</h1>';
    if (!class_exists('WPCF7_Contact_Form_List_Table')) {
        require_once WPCF7_PLUGIN_DIR . '/admin/includes/class-contact-forms-list-table.php';
    }

    $list_table = new WPCF7_Contact_Form_List_Table();
    $list_table->prepare_items();

    $first_form_id = 0;
    $fid = ((isset($_GET['fid'])) ? (int) $_GET['fid'] : '');
    $search = ((isset($_GET['search'])) ? addslashes($_GET['search']) : '');

    echo '<form action="'.admin_url('admin.php').'" method="GET">';
    echo '<input type="hidden" name="page" value="cf7-data" />';
    echo '<select name="fid" onchange="this.form.submit();">';
    echo '<option value="" '.(( count($list_table->items) == 0 ) ? 'selected="selected"' : '').' >'.__('Choose form').'</option>';
    $i = 0;
    foreach ($list_table->items as $k => $v) {
        if ($first_form_id == 0 && $i == 0) {
            $first_form_id = $v->id;

            if (empty($fid) && ($first_form_id > 0)) {
                $fid = $first_form_id;
            }
        }
        echo '<option value="'.$v->id.'" '.(( !empty($fid) && ($fid == $v->id)) ? 'selected="selected"' : '').'>'.$v->title.'</option>';
    }
    echo '</select>';
    echo '</form>';
    
    if (!empty($fid)) {
        
        $cf7d_entry_order_by = apply_filters('cf7d_entry_order_by', '`data_id` DESC');
        $cf7d_entry_order_by = trim($cf7d_entry_order_by);

        $items_per_page = apply_filters('cf7d_entry_per_page', 20);
        $page = isset($_GET['cpage']) ? abs((int)$_GET['cpage']) : 1;
        $offset = ( $page * $items_per_page ) - $items_per_page;

        $query = sprintf("SELECT * FROM `".$wpdb->prefix."cf7_data_entry` WHERE `cf7_id` = %d AND data_id IN(SELECT * FROM (SELECT data_id FROM `".$wpdb->prefix."cf7_data_entry` WHERE 1 = 1 AND `cf7_id` = ".$fid." ".((!empty($search)) ? "AND `value` LIKE '%%".$search."%%'" : "")." GROUP BY `data_id` ORDER BY ".$cf7d_entry_order_by." LIMIT %d,%d) temp_table) ORDER BY " . $cf7d_entry_order_by, $fid, $offset, $items_per_page);
        $data = $wpdb->get_results($query);
        $data_sorted = cf7d_sortdata($data);

        $fields = cf7d_get_db_fields($fid);

        $total = $wpdb->get_results("SELECT data_id FROM `".$wpdb->prefix."cf7_data_entry` WHERE `cf7_id` = " . (int)$fid . " ".((!empty($search)) ? "AND `value` LIKE '%%".$search."%%'" : "")." GROUP BY `data_id`");
        $total = count($total);

        $entry_actions = array(
                'delete' => 'Delete'
            );
        $entry_actions = apply_filters('cf7d_entry_actions', $entry_actions);
        ?>
        <form action="" method="GET" id="cf7d-admin-action-frm">
            <input type="hidden" name="page" value="cf7-data">
            <input type="hidden" name="fid" value="<?php echo $fid; ?>">
            <div class="tablenav top">
                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-top" class="screen-reader-text"><?php _e('Select bulk action');?></label>
                    <select name="action" id="bulk-action-selector-top">
                        <option value="-1"><?php _e('Bulk Actions'); ?></option>
                        <?php echo cf7d_arr_to_option($entry_actions); ?>
                    </select>
                    <input id="doaction" name="btn_apply" class="button action" value="<?php _e('Apply'); ?>" type="submit" />
                    <?php do_action('cf7d_after_bulkaction_btn', $fid); ?>                    
                </div>
                <div class="tablenav-pages">
                    <span class="displaying-num"><?php echo (($total == 1) ?
                    '1 ' . __('item') :
                    $total . ' ' . __('items')) ?></span>
                    <span class="pagination-links">
                        <?php
                        echo paginate_links(array(
                            'base' => add_query_arg('cpage', '%#%'),
                            'format' => '',
                            'prev_text' => __('&laquo;'),
                            'next_text' => __('&raquo;'),
                            'total' => ceil($total / $items_per_page),
                            'current' => $page
                        ));
                        ?>
                    </span>
                </div>
                <br class="clear">
            </div>
            <table class="wp-list-table widefat fixed striped posts cf7d-admin-table">
                <thead>
                    <tr>
                        <?php
                        echo '<td id="cb" class="manage-column column-cb check-column"><input type="checkbox" id="cb-select-all-1" /></td>';
                        foreach ($fields as $k => $v) {
                            echo '<th class="manage-column" data-key="'.$v.'">'.cf7d_admin_get_field_name($v).'</th>';
                        }
                        ?>
                        <?php do_action('cf7d_admin_after_heading_field'); ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($data_sorted as $k => $v) {
                        echo '<tr>';
                        echo '<th class="check-column" scope="row"><input id="cb-select-'.$k.'" type="checkbox" name="del_id[]" value="'.$k.'" /></th>';
                        foreach ($fields as $k2 => $v2) {
                            $_value = ((isset($v[$k2])) ? $v[$k2] : '&nbsp;');
                            if (!filter_var($_value, FILTER_VALIDATE_URL) === false) {
                                $_value = sprintf('<a href="%s" target="_blank">%s</a>', $_value, $_value);
                            }
                            echo '<td data-head="'.cf7d_admin_get_field_name($v2).'">'.$_value.'</td>';
                        }
                        $row_id = $k;
                        do_action('cf7d_admin_after_body_field', $fid, $row_id);
                        echo '</tr>';
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <?php
                        echo '<td class="manage-column column-cb check-column"><input type="checkbox" id="cb-select-all-2" /></td>';
                        foreach ($fields as $k => $v) {
                            echo '<th class="manage-column" data-key="'.$v.'">'.cf7d_admin_get_field_name($v).'</th>';
                        }
                        ?>
                        <?php do_action('cf7d_admin_after_heading_field'); ?>
                    </tr>
                </tfoot>
            </table>
            <div class="tablenav bottom">
                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-bottom" class="screen-reader-text"><?php _e('Select bulk action'); ?></label>
                    <select name="action2" id="bulk-action-selector-bottom">
                        <option value="-1"><?php _e('Bulk Actions'); ?></option>
                        <?php echo cf7d_arr_to_option($entry_actions); ?>
                    </select>
                    <input id="doaction2" class="button action" value="<?php _e('Apply'); ?>" type="submit" name="btn_apply2" />
                </div>
                <div class="tablenav-pages">
                    <span class="displaying-num"><?php echo (($total == 1) ?
                    '1 ' . __('item') :
                    $total . ' ' . __('items')) ?></span>
                </div>
                <br class="clear">
            </div>            
        </form>
        <?php do_action('cf7d_after_admin_form', $fid); ?>        
        <?php
    } else {
        if ($first_form_id > 0 && !(isset($_GET['action'])) && !(isset($_GET['action2']))) {
            ?>
            <script>
            location.replace('<?php echo admin_url("admin.php?page=cf7-data&fid=" . $first_form_id); ?>');
            </script>
            <?php
        }
    }
    echo '</div>';
}
function cf7d_form_action_callback()
{
    global $wpdb;
    if ($current_action = cf7d_current_action()) {
        if ($current_action == 'delete') {
            if (isset($_GET['del_id'])) {
                $del_id = implode(',', $_GET['del_id']);
                $fid = (int)$_GET['fid'];
                $wpdb->query("DELETE FROM {$wpdb->prefix}cf7_data_entry WHERE data_id IN($del_id)");
                $wpdb->query("DELETE FROM {$wpdb->prefix}cf7_data WHERE id IN($del_id)");
                wp_safe_redirect(admin_url('admin.php?page=cf7-data&fid=' . $fid));
                exit();
            }
        }
        do_action('cf7d_entry_action', $current_action);
    }
    do_action('cf7d_main_post');
}
function cf7d_current_action()
{
    $current_action = false;
    if (isset($_REQUEST['action']) && -1 != $_REQUEST['action'] && isset($_GET['btn_apply'])) {
        $current_action = $_REQUEST['action'];
        return apply_filters('cf7d_get_current_action', $current_action);
    }

    if (isset($_REQUEST['action2']) && -1 != $_REQUEST['action2'] && isset($_GET['btn_apply2'])) {
        $current_action = $_REQUEST['action2'];
        return apply_filters('cf7d_get_current_action', $current_action);
    }
    $current_action = apply_filters('cf7d_get_current_action', $current_action);
    return false;
}
