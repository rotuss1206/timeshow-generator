<?php
global $wpdb;

$table = 'timeshow_parameters';
    $users = $wpdb->users;

    $per_page = 20;
    $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($paged - 1) * $per_page;

    $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table");

    $results = $wpdb->get_results(
	    $wpdb->prepare(
	        "SELECT
	            t.timeshow_name,
	            t.upload_time,
	            u.ID AS user_id,
	            u.display_name,
	            u.user_login,
	            u.user_email
	        FROM $table t
	        LEFT JOIN $users u ON t.user_id = u.ID
	        ORDER BY t.upload_time DESC
	        LIMIT %d OFFSET %d",
	        $per_page,
	        $offset
	    )
	);
    ?>

    <div class="wrap">
        <h1 class="wp-heading-inline">Timeshow Statistics</h1>

        <a href="<?php echo admin_url('admin-post.php?action=timeshow_export_excel'); ?>"
           class="page-title-action">
           Download Excel
        </a>

        <table class="widefat fixed striped" style="margin-top:20px;">
            <thead>
                <tr>
                    <th>Timeshow Name</th>
                    <th>User</th>
                    <th>Upload Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($results): ?>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td><?php echo esc_html($row->timeshow_name); ?></td>
                            <td>
							    <?php
							        if ($row->user_id) {
							            $name = $row->display_name ?: $row->user_login;
							            echo esc_html(
							                '#' . $row->user_id . '  ' .
							                $name .
							                ' (' . $row->user_login . ')' .
							                ' <' . $row->user_email . '>'
							            );
							        } else {
							            echo '<em>Unknown user</em>';
							        }
							    ?>
							</td>
                            <td><?php echo esc_html($row->upload_time); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No data found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php
        $total_pages = ceil($total_items / $per_page);

        if ($total_pages > 1){
            echo '<div class="tablenav"><div class="tablenav-pages">';
            echo paginate_links([
                'base' => add_query_arg('paged', '%#%'),
                'format' => '',
                'prev_text' => '«',
                'next_text' => '»',
                'total' => $total_pages,
                'current' => $paged
            ]);
            echo '</div></div>';
        }
        ?>
    </div>
<style>
	.tablenav .tablenav-pages{
		float: left;
		font-size: 16px;
	}
</style>
