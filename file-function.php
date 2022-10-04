<?php
global $wpdb;
$msg = '';

$action = isset($_GET['action']) ? trim($_GET['action']) : "";
$id = isset($_GET['id']) ? intval($_GET['id']) : "";

$row_details = $wpdb->get_row(
        $wpdb->prepare(
                "SELECT * from wp_custom_api WHERE id = %d", $id
        ), ARRAY_A
);


if (isset($_POST['btnsubmit'])) {

    $action = isset($_GET['action']) ? trim($_GET['action']) : "";
    $id = isset($_GET['id']) ? intval($_GET['id']) : "";

    if (!empty($action)) {

        $wpdb->update("wp_custom_api", array(
            "api_key" => $_POST['api_key'],
            "list_id" => $_POST['list_id']
                ), array(
            "id" => $id
        ));

        $msg = "Api key and list id successfully updated";
    } else {

        $wpdb->insert("wp_custom_api", array(
            "api_key" => $_POST['api_key'],
            "list_id" => $_POST['list_id']
        ));

        if ($wpdb->insert_id > 0) {
            $msg = "Great you have added the api and list id";
        } else {
            $msg = "Failed to save key";
        }
    }
}
?>

<p><?php echo $msg; ?></p>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?page=submit-api<?php
if (!empty($action)) {
    echo '&action=edit&id=' . $id;
}
?>" method="post">
    <p>
        <label>
            API KEY
        </label>
        <input type="text" name="api_key" value="<?php echo isset($row_details['api_key']) ? $row_details['api_key'] : ""; ?>" placeholder=" api key" size="40"/>
    </p>
    <p>
        <label>
            LIST ID
        </label>&nbsp;
        <input type="text" name="list_id" value="<?php echo isset($row_details['list_id']) ? $row_details['list_id'] : ""; ?>" placeholder=" list id" size="40"/>
    </p>
    <p>
        <button type="submit" clas="btn btn-primary" name="btnsubmit">Add key</button>
    </p>
</form>