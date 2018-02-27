<?php
// Prepare the headers for JSON data
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");

require 'common.php';
require 'config.php';
$conn = new mysqli($php_server, $php_user, $php_password, $php_schema);
mysqli_set_charset($conn, 'utf8');

$url = $_GET["_url"];
log_this($url);

$request = explode('/', trim($url, '/'));
$table = preg_replace('/[^a-z0-9_]+/i', '', array_shift($request));
$key = array_shift($request);

switch ($_SERVER['REQUEST_METHOD']) {
  case "OPTIONS":
    header('Access-Control-Allow-Credentials:true');
    header('Access-Control-Allow-Headers:X-Requested-With,content-type');
    header('Access-Control-Allow-Methods:GET, POST, OPTIONS, PUT, PATCH, DELETE');
    header('Access-Control-Allow-Origin:*');
    header('Allow:PUT,DELETE');
    header('Connection:keep-alive');
    header('Content-Length:10');
    header('Content-Type:application/json; charset=utf-8');
    break;
  case "GET":
    switch($table) {
    case "login":
      $query = "SELECT * FROM user WHERE user_name = '" . $_GET['user_name'] . "' AND password = '" . $_GET['password'] . "'";
      break;
    case "budget":
      $query = "SELECT `budget`.`id`, `budget`.`branch_id`, date_format(`date`, '%d-%m-%y') AS `date`, date_format(`date`, '%Y-%m-%d') AS `_date`, `budget`.`hours`, `budget`.`footer`, weekday(`date`) AS `weekday`, date_format(`budget`.`created_at`, '%d-%b-%y') AS `created_at`, date_format(`budget`.`updated_at`, '%d-%b-%y') AS `updated_at`, `branch`.`name` AS `branch.name` FROM `budget` AS `budget` INNER JOIN `branch` AS `branch` ON `budget`.`branch_id` = `branch`.`id` AND `budget`.`branch_id` ORDER BY `budget`.`date` DESC;";
      break;
    case "sector":
      $query = "SELECT `id`, `name`, date_format(`sector`.`created_at`, '%d-%b-%y') AS `created_at`, date_format(`sector`.`updated_at`, '%d-%b-%y') AS `updated_at` FROM `sector` AS `sector` WHERE `sector`.`name` LIKE '%%' ORDER BY `sector`.`name` ASC LIMIT 0, 1000;";
      break;
    case "position":
      $query = "SELECT `position`.`id`, `position`.`name`, `position`.`sector_id`, `position`.`color`, date_format(`position`.`created_at`, '%d-%b-%y') AS `created_at`, date_format(`position`.`updated_at`, '%d-%b-%y') AS `updated_at`, `sector`.`name` AS `sector.name` FROM `position` AS `position` INNER JOIN `sector` AS `sector` ON `position`.`sector_id` = `sector`.`id` AND `position`.`sector_id` WHERE `position`.`name` LIKE '%%' ORDER BY `position`.`name` ASC LIMIT 0, 1000;";
      break;
    case "branch":
      $query = "SELECT `branch`.`id`, `branch`.`name`, `branch`.`status_id`, date_format(`branch`.`created_at`, '%d-%b-%y') AS `created_at`, date_format(`branch`.`updated_at`, '%d-%b-%y') AS `updated_at`, `status`.`name` AS `status.name` FROM `branch` AS `branch` INNER JOIN `status` AS `status` ON `branch`.`status_id` = `status`.`id` AND `branch`.`status_id`;";
      break;
    case "employee":
      $query = "SELECT `employee`.`id`, `employee`.`badge`, `employee`.`last_name`, `employee`.`first_name`, `employee`.`badge`, `employee`.`status_id`, `employee`.`branch_id`, date_format(`employee`.`joining_date`, '%Y-%m-%d') AS `joining_date`, date_format(`employee`.`joining_date`, '%d-%b-%y') AS `_joining_date`, date_format(`employee`.`created_at`, '%d-%b-%y') AS `created_at`, date_format(`employee`.`updated_at`, '%d-%b-%y') AS `updated_at`, `status`.`name` AS `status.name`, `branch`.`name` AS `branch.name` FROM `employee` AS `employee` INNER JOIN `status` AS `status` ON `employee`.`status_id` = `status`.`id` AND `employee`.`status_id` INNER JOIN `branch` AS `branch` ON `employee`.`branch_id` = `branch`.`id` AND `employee`.`branch_id` ORDER BY `employee`.`badge` ASC LIMIT 0, 1000;";
      break;
    case "user":
      $query = "SELECT `user`.`id`, `user`.`user_name`, `user`.`full_name`, `user`.`status_id`, `user`.`profile_id`, date_format(`user`.`created_at`, '%d-%b-%y') AS `created_at`, date_format(`user`.`updated_at`, '%d-%b-%y') AS `updated_at`, `status`.`name` AS `status.name`, `profile`.`name` AS `profile.name` FROM `user` AS `user` INNER JOIN `status` AS `status` ON `user`.`status_id` = `status`.`id` AND `user`.`status_id` INNER JOIN `profile` AS `profile` ON `user`.`profile_id` = `profile`.`id` AND `user`.`profile_id` WHERE `user`.`full_name` LIKE '%%' ORDER BY `user`.`full_name` ASC LIMIT 0, 1000;";
      break;
    case "schedule":
      $query = "SELECT @budget_id:=`budget`.`id`, `budget`.`id`, `budget`.`date`, weekday(`date`) AS `weekday`, weekday(`date`) AS `_weekday`, date_format(`date`, '%Y-%m-%d') AS `_date`, `budget`.`hours`, `budget`.`footer`, `budget`.`branch_id`, date_format(`date`, '%d-%m-%Y') AS `date`, `branch`.`name` AS `branch.name` FROM `budget` AS `budget` INNER JOIN `branch` AS `branch` ON `budget`.`branch_id` = `branch`.`id` AND `budget`.`branch_id` WHERE `budget`.`date` = '2018-02-01' AND `budget`.`branch_id` = '1' LIMIT 1;SELECT `schedule`.`id`, `schedule`.`from`, `schedule`.`to`, `schedule`.`employee_id`, `schedule`.`position_id`, date_format(`schedule`.`created_at`, '%d-%b-%y') AS `created_at`, date_format(`schedule`.`updated_at`, '%d-%b-%y') AS `updated_at`, `employee`.`badge` AS `employee.badge`, `employee`.`first_name` AS `employee.first_name`, `employee`.`last_name` AS `employee.last_name`, `position`.`name` AS `position.name`, `position`.`color` AS `position.color`, `position->sector`.`id` AS `position.sector.id`, `position->sector`.`name` AS `position.sector.name` FROM `schedule` AS `schedule` INNER JOIN `employee` AS `employee` ON `schedule`.`employee_id` = `employee`.`id` AND `schedule`.`employee_id` INNER JOIN `position` AS `position` ON `schedule`.`position_id` = `position`.`id` AND `schedule`.`position_id` INNER JOIN `sector` AS `position->sector` ON `position`.`sector_id` = `position->sector`.`id` AND `position`.`sector_id` WHERE `schedule`.`budget_id` = @budget_id ORDER BY `schedule`.`employee_id` ASC, `schedule`.`from` ASC, `schedule`.`to` ASC;";
      break;
    default:
      $query = "SELECT * FROM " . $table;
      if ($key <> null) {
          $query = $query . " WHERE id=" . $key;
      }
    }
    $result = $conn->query($query);
    $rows = array();
    if ($result) {
        while ($row = $result->fetch_array(MYSQLI_ASSOC))
            {
            $rows[] = $row;
        }
        $result->free();
    }
    if($table !== "login") {
      $results["model"] = $table;
      $results["count"] = count($rows);
      $results["rows"] = $rows;
    } else {
      $results = $rows;
    }
    $conn->close();
    break;
  case "POST":
    $input = json_decode(file_get_contents("php://input"), true);
    $columns = preg_replace('/[^a-z0-9_]+/i', '', array_keys($input));
    $values = array_map(function ($value) use ($conn) {
        if ($value === null) return null;
        return mysqli_real_escape_string($conn, (string)$value);
    }, array_values($input));

    $columns_ = '';
    $values_ = '';
    for ($i = 0; $i < count($columns); $i++) {
        $columns_ .= ($i > 0 ? ',' : '') . '`' . $columns[$i] . '`';
        $values_ .= ($i > 0 ? ',' : '') . '"' . $values[$i] . '"';
    }
    $sql = "INSERT INTO `$table` ($columns_) values ($values_)";
    $result = $conn->query($sql);
    $last_id = $conn->insert_id;
    $results["success"] = $result;
    $results["record_id"] = $last_id;
    $conn->close();
    break;
  case "PUT":
    $input = json_decode(file_get_contents("php://input"), true);
    $columns = preg_replace('/[^a-z0-9_]+/i', '', array_keys($input));
    $values = array_map(function ($value) use ($conn) {
        if ($value === null) return null;
        return mysqli_real_escape_string($conn, (string)$value);
    }, array_values($input));

    $set = '';
    for ($i = 0; $i < count($columns); $i++) {
      if($columns[$i]<>'id') {
        $set .= ($i > 0 ? ',' : '') . '`' . $columns[$i] . '`=';
        $set .= ($values[$i] === null ? 'NULL' : '"' . $values[$i] . '"');
      }
    }
    $set .= ',updated_at=NOW()';
    $sql = "UPDATE `$table` SET $set WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $key);
    $stmt->execute();
    $results["result"] = "Updated";
    $conn->close();
    break;
  case "DELETE":
    $sql = "DELETE FROM `$table` WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $key);
    $stmt->execute();
    $results["status"] = true;
    $conn->close();
    break;
}
$json = json_encode($results);
if(($table) === "login") {
  $json = str_replace("[", "", $json);
  $json = str_replace("]", "", $json);
  echo ($json);
} else {
  echo ($json);
}
?>
