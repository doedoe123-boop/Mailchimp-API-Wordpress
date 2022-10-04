<?php


global $wpdb;
$all_list = $wpdb->get_results(
        $wpdb->prepare(
                "SELECT * from wp_custom_api", ""
        ), ARRAY_A
);

$action = isset($_GET['action']) ? trim($_GET['action']) : "";
$id = isset($_GET['id']) ? intval($_GET['id']) : "";
if (!empty($action) && $action == "delete") {

    $row_exists = $wpdb->get_row(
            $wpdb->prepare(
                    "SELECT * from wp_custom_api WHERE id = %d", $id
            )
    );
    if (count($row_exists) > 0) {
        $wpdb->delete("wp_custom_api", array(
            "id" => $id
        ));
    }
    ?>
    <script>
        location.href = "<?php echo site_url() ?>/wp-admin/admin.php?page=wc-acutions-list-keys";
    </script>
    <?php
}

if (count($all_list) > 0) {
    ?><br>
    <table cellpadding="10" border="1">
         <thead class="thead-dark">
        <tr>
          
            <th scope="col">API KEY</th>
            <th>LIST ID</th>
            <th>ACTION</th>
            </thead>
        </tr>
        <?php
        $count = 1;
        foreach ($all_list as $index => $item) {
            ?>
            <tr>
               
                <td><?php echo $item['api_key'] ?></td>
                <td><?php echo $item['list_id'] ?></td>
                <td>
                    <a href="admin.php?page=submit-api&action=edit&id=<?php echo $item['id']; ?>">Update</a> 
                
                </td>
            </tr>
            <?php
        }
        ?>
    </table>

    <?php
}
?>