<?php
require_once('MysqliDb.php');
error_reporting(E_ALL);
$action = 'adddb';
$data = array();



function printUsers() {
    global $db;
    $users = $db->get("users");

    if ($db->count == 0) {
        echo "<td align=center colspan=4>No users found</td>";
        return;
    }
    foreach ($users as $u) {
        echo "<tr>
            <td>{$u['id']}</td>
            <td>{$u['login']}</td>
            <td>{$u['firstName']} {$u['lastName']}</td>
            <td>
                <a href='index.php?action=rm&id={$u['id']}'>rm</a> ::
                <a href='index.php?action=mod&id={$u['id']}'>ed</a>
            </td>
        </tr>";
    }
}

function printRegions() {
    global $db;
    $regions = $db->get('regions');
    //$regions = $db->get("couries");
    if ($db->count == 0) {
        echo "<td align=center colspan=4>No users found</td>";
        return;
    }
    foreach ($regions as $u) {
        echo "<tr>
            <td>{$u['id']}</td>
            <td>{$u['name']}</td>
            <td>{$u['duration']}</td>
        </tr>";
    }
}

function printSchedule() {
    global $db;
    $regions = $db->get('schedule');
    //$regions = $db->get("couries");
    if ($db->count == 0) {
        echo "<td align=center colspan=4>No users found</td>";
        return;
    }
    foreach ($regions as $u) {
        echo "<tr>
            <td>{$u['id']}</td>
            <td>{$u['courierid']}</td>
            <td>{$u['regionid']}</td>
            <td>{$u['depart']}</td>
            <td>{$u['arrive']}</td>
        </tr>";
    }
}

function printCouriers() {
    global $db;
    $сouriers = $db->get('сouriers');
    //$сouriers = $db->get("couries");
    if ($db->count == 0) {
        echo "<td align=center colspan=4>No users found</td>";
        return;
    }
    foreach ($сouriers as $u) {
        echo "<tr>
            <td>{$u['id']}</td>
            <td>{$u['name']} {$u['name']}</td>
        </tr>";
    }
}

function action_adddb() {
    global $db;
    $data = Array(
        'login' => $_POST['login'],
        'customerId' => 1,
        'firstName' => $_POST['firstName'],
        'lastName' => $_POST['lastName'],
        'password' => $db->func('SHA1(?)', Array($_POST['password'] . 'salt123')),
        'createdAt' => $db->now(),
        'expires' => $db->now('+1Y')
    );
    $id = $db->insert('users', $data);
    header("Location: index.php");
    exit;
}

function action_moddb() {
    global $db;
    $data = Array(
        'login' => $_POST['login'],
        'customerId' => 1,
        'firstName' => $_POST['firstName'],
        'lastName' => $_POST['lastName'],
    );
    $id = (int) $_POST['id'];
    $db->where("customerId", 1);
    $db->where("id", $id);
    $db->update('users', $data);
    header("Location: index.php");
    exit;
}

function action_rm() {
    global $db;
    $id = (int) $_GET['id'];
    $db->where("customerId", 1);
    $db->where("id", $id);
    $db->delete('users');
    header("Location: index.php");
    exit;
}

function action_mod() {
    global $db;
    global $data;
    global $action;
    $action = 'moddb';
    $id = (int) $_GET['id'];
    $db->where("id", $id);
    $data = $db->getOne("users");
}

/* $mysqli = new mysqli('host', 'username', 'password', 'databaseName');
  $db = new MysqliDb($mysqli); */

$db = new Mysqlidb("localhost", "torick_instrum", "Wt&X%x4P", "torick_instrum");
if ($_GET) {
    $f = "action_" . $_GET['action'];
    if (function_exists($f)) {
        $f();
    }
}
?>
<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Расписание</title>
        <link rel="stylesheet" href="/datepicker/css/bootstrap-datepicker.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        <script src="/datepicker/js/bootstrap-datepicker.js" ></script>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-12">

                    <h3>Курьеры</h3>
                    <table width='50%'>
                        <tr bgcolor='#cccccc'>
                            <th>ID</th>
                            <th>Name</th>
                        </tr>
                        <?php printCouriers(); ?>

                    </table>
                    <br>
                    <br>
                    <h3>Регионы</h3>
                    <table width='50%'>
                        <tr bgcolor='#cccccc'>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Duration</th>
                        </tr>
                        <?php printRegions(); ?>

                    </table>
                    <br>
                    <br>
                    <h3>Расписание</h3>
                    <table width='50%'>
                        <tr bgcolor='#cccccc'>
                            <th>ID</th>
                            <th>Регион</th>
                            <th>Курьер</th>
                            <th>Дата отправления</th>
                            <th>Дата возрата</th>
                        </tr>
                        <?php printSchedule(); ?>

                    </table>
                    <br>
                    <br>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5">
                    <div class="contact-form">
                        <form action='index.php?action=<?php echo $action ?>' method=post>
                            <input type=hidden name='id' value='<?php echo $data['id'] ?>'>
                            <div class="messages"></div>
                            <div class="controls">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleFormControlSelect1">Регион</label>
                                            <select class="form-control" id="exampleFormControlSelect1">
                                                <option>1</option>
                                                <option>2</option>
                                                <option>3</option>
                                                <option>4</option>
                                                <option>5</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleFormControlSelect1">Курьер</label>
                                            <select class="form-control" id="exampleFormControlSelect1">
                                                <option>1</option>
                                                <option>2</option>
                                                <option>3</option>
                                                <option>4</option>
                                                <option>5</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="exampleFormControlSelect1">Дата</label>
                                            <div class="input-group date">
                                                <input id="datetimepicker" type="text" class="form-control" />
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <script type="text/javascript">
                                        $(function () {
                                            $('#datetimepicker').datepicker({
                                            });
                                        });
                                    </script>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="submit" class="btn btn-effect btn-sent" value="Send message">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>