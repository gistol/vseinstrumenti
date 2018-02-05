<?php
/* ini_set('error_reporting', E_ALL);
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1); */

require_once('classes/DB/MysqliDb.php');
$db = new Mysqlidb("localhost", "torick_instrum", "Wt&X%x4P", "torick_instrum");
$message_show = FALSE;

if (isset($_POST['submit']) && $_POST['submit'] == 'Add') {

    $message_show = TRUE;
    $courier_id = $_POST['courier_id'];
    $region_id = $_POST['region_id'];
    $start_date_unix = strtotime($_POST['start_date']);

    if (empty($courier_id) && is_numeric($courier_id) || empty($region_id) && is_numeric($region_id) || empty($start_date_unix) && is_numeric($start_date_unix)) {
        $message = 'Отсутствуют необходимые данные для их записи в БД.<br />';
    } else {
        $db->join("couriers c", "e.courier_id=c.id", "LEFT");
        $db->joinWhere("couriers c", "c.id", $courier_id);
        $db->where("e.courier_id", $courier_id);
        $db->orderBy("e.end_date_unix", "desc");
        $events = $db->get("events e", 1, "c.*, e.start_date_unix, e.end_date_unix");

        $regions = $db->get("regions r", null, "r.*");


        foreach ($events as $event) {
            if ($event['end_date_unix'] < $start_date_unix) {
                $message = 'Курьер свободен. Поездка добавлена.<br />';
                $event_id = $db->insert('events', array('courier_id' => $courier_id, 'region_id' => $region_id, 'start_date_unix' => $start_date_unix, 'end_date_unix' => $start_date_unix + $regions[$region_id]['duration']));
            } else {
                $message = 'Курьер занят в это время. Данные не сохранены.<br />';
            }
        }
    }
}

function selectRegions() {
    global $db;
    $regions = $db->get('regions');
    echo "<select name='region_id' class='form-control' id='select_regions'>";
    if ($db->count == 0) {
        echo "<option value='none'>Регионов не найдено</option>";
        return;
    }
    foreach ($regions as $u) {
        $selected = $_POST['region_id'] == $u['id'] ? 'selected' : '';
        echo "<option {$selected} value='{$u['id']}'>{$u['name']}</option>";
    }
    echo "</select>";
}

function selectCouriers() {
    global $db;
    $сouriers = $db->get('couriers');
    echo "<select name='courier_id' class='form-control' id='couriers'>";
    if ($db->count == 0) {
        echo "<option value='none'>Курьеров не найдено</option>";
        return;
    }
    foreach ($сouriers as $u) {
        $selected = $_POST['courier_id'] == $u['id'] ? 'selected' : '';
        echo "<option {$selected} value='{$u['id']}'>{$u['name']}</option>";
    }
    echo "</select>";
}

function printRegions() {
    global $db;
    $regions = $db->get('regions');
    if ($db->count == 0) {
        echo "<td align=center colspan=4>Регионов не найдено</td>";
        return;
    }
    foreach ($regions as $u) {
        $duration = date('d', $u['duration']);
        echo "<tr>
            <td>{$u['id']}</td>
            <td>{$u['name']}</td>
            <td>{$duration}</td>
        </tr>";
    }
}

function printEvents() {
    global $db;
    $regions = $db->get("regions");
    foreach ($regions as $region) {
        $region_array[$region['id']] = $region['name'];
    }
    $couriers = $db->get("couriers");
    foreach ($couriers as $courier) {
        $courier_array[$courier['id']] = $courier['name'];
    }
    $db->orderBy("start_date_unix", "desc");
    $events = $db->get('events', 100);
    if ($db->count == 0) {
        echo "<td align=center colspan=4>Событый не найдено</td>";
        return;
    }
    foreach ($events as $u) {
        $date_start = date('Y/m/d', $u['start_date_unix']);
        $date_end = date('Y/m/d', $u['end_date_unix']);
        echo "<tr>
            <td>{$u['id']}</td>
            <td>{$region_array[$u['region_id']]}</td>
            <td>{$courier_array[$u['courier_id']]}</td>
            <td>{$date_start}</td>
            <td>{$date_end}</td>
        </tr>";
    }
}

function printCouriers() {
    global $db;
    $сouriers = $db->get('couriers');
    if ($db->count == 0) {
        echo "<td align=center colspan=4>Курьеров не найдено</td>";
        return;
    }
    foreach ($сouriers as $u) {
        echo "<tr>
            <td>{$u['id']}</td>
            <td>{$u['name']} {$u['name']}</td>
        </tr>";
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
                    <table width='80%'>
                        <tr bgcolor='#cccccc'>
                            <th>ID</th>
                            <th>Регион</th>
                            <th>Курьер</th>
                            <th>Дата отправления</th>
                            <th>Дата возрата</th>
                        </tr>
                        <?php printEvents(); ?>

                    </table>
                    <br>
                    <br>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="contact-form">
                        <form id="add-event" method="POST" action="<?= $PHP_SELF ?>">
                            <div class="controls">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleFormControlSelect1">Регион</label>
                                            <?php selectRegions(); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleFormControlSelect1">Курьер</label>
                                            <?php selectCouriers(); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="exampleFormControlSelect1">Дата</label>
                                            <div class="input-group date">
                                                <input value="<?= $_POST['start_date'] ? $_POST['start_date'] : "" ?>" data-date-format='yyyy/mm/dd' id="datetimepicker" type="text" name="start_date" class="form-control" />
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="submit" name="submit" class="btn btn-effect btn-sent" value="Add">
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div><p><?= $message_show ? $message : "" ?></p></div>
                        <script  type="text/javascript">
                            $(document).ready(function () {
                                $('#add-event').submit(function (event) {
                                    var values = $(this).serialize();
                                    $.ajax({
                                        type: $('#add-event').attr('method'),
                                        url: $('#add-event').attr('action'),
                                        data: values,
                                        success: function (data) {
                                            console.log('Submission was successful.');
                                            console.log(data);
                                        },
                                        error: function (data) {
                                            console.log('An error occurred.');
                                            console.log(data);
                                        },
                                    });
                                });
                                $(function () {
                                    $('#datetimepicker').datepicker({

                                    });
                                });
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>