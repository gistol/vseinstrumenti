<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);



// Убираем лимит времени на выполнение скрипта
set_time_limit(0);

require_once('classes/DB/MysqliDb.php');
$db = new Mysqlidb("localhost", "torick_instrum", "Wt&X%x4P", "torick_instrum");
//$db = new MysqliDb('localhost', 'root', '', 'stas');
// Есть 3 таблицы: couriers, regions, events
// Начальное время, от которого мы будем добавлять события и финальное время, до которого мы будем добавлять
$current_date_unix = strtotime("2018/01/01");
$end_date_unix = time();
$one_day = 86400;

$couriers = array(); // Тут у нас все курьеры и у каждого курьера информация о начале маршрута и времени, которое займет маршрут
$regions = array(); // Тут просто все регионы
// Получаем курьеров и их занятость из таблицы events:
$db->join("(SELECT * FROM events ORDER BY events.end_date_unix DESC LIMIT 1) AS e", "c.id=e.courier_id", "LEFT");
$couriers = $db->get("сouriers c", null, "c.*, e.start_date_unix, e.end_date_unix");

// Получаем регионы и информацию о том, движется ли сейчас какой-нибудь курьер в город
$db->join("(SELECT * FROM events ORDER BY events.end_date_unix DESC LIMIT 1) AS e", "r.id=e.region_id", "LEFT");
$regions = $db->get("regions r", null, "r.*, e.start_date_unix, e.end_date_unix");
$regions_num = sizeof($regions);

// Включаем цикл до тех пор, пока нам требуется распределять курьеров до конца нужного срока
$timemanager_enabled = TRUE;
$rotation = array();

while ($timemanager_enabled)
{
	// Берем с запасом, сколько возможно потребуется максимально дней, ну точно не больше 100
	$closest_date_unix = $current_date_unix + ($one_day * 100);
	$new_end_date_unix = $current_date_unix;

	//die('Time-manager');

	echo 'Смотрим курьеров - ' . date('Y/m/d H:i', $current_date_unix) . '<br />';
	ob_end_flush();


	// Прорходимся по всем курьерам
	foreach($couriers as $courier_key => $courier)
	{
		//print_r($current_date_unix);
		//die($courier['end_date_unix'] . ' < ' . $current_date_unix);

		// Если курьер свободен, то
		if ($courier['end_date_unix'] < $current_date_unix)
		{
			echo $courier['name'] . ' свободен. Закончил поездку ' . date('Y/m/d H:i', $courier['end_date_unix']) . '<br />';
			ob_end_flush();

			//die('Ищем регион');

			// Записываем, когда работяга освободился
			$new_end_date_unix = $courier['end_date_unix'];

			// Пытаемся найти город для него
			$city_finded = false;

			// Проходимся по регионам и ищем свободные
			$free_regions = array();
			foreach($regions as $region_key => $region)
			{
				// Если в регион никто не едет до текущего времени, то можно отнести регион к свободным на данный момент
				if ($region['end_date_unix'] < $current_date_unix)
				{
					// Город найден

					if (isset($region['name']) && isset($region['id']))
					{
						$region['real_key'] = $region_key;
						$free_regions[] = $region;
					}

				}
			}

			//print_r($free_regions);

			// Если есть свободные регионы
			if (sizeof($free_regions) > 0)
			{
				// Проверяем, есть ли кто-то на ротации, если есть, то меняем с текущим
				$rotated = false;
				if (sizeof($rotation) > 0)
				{
					// Меняем местами курьеров
					$temp_rotation_key = array_shift($rotation);
					list($couriers[$courier_key], $couriers[$temp_rotation_key]) = array($couriers[$temp_rotation_key], $couriers[$courier_key]);
					$rotated = true;
				}

				// То ищем произвольный регион
				$random_region = rand(0, (sizeof($free_regions)-1));

				// Отправляем курьера, добавляем событие
				$new_end_date_unix = $current_date_unix + $regions[$free_regions[$random_region]['real_key']]['duration'];

				$event_id = $db->insert('events', array('courier_id' => $courier['id'], 'region_id' => $free_regions[$random_region]['id'], 'start_date_unix' => $current_date_unix, 'end_date_unix' => $new_end_date_unix));

				// Обновляем информацию курьеру и региону
				$couriers[$courier_key]['start_date_unix'] = $regions[$free_regions[$random_region]['real_key']]['start_date_unix'] = $current_date_unix;
				$couriers[$courier_key]['end_date_unix'] = $regions[$free_regions[$random_region]['real_key']]['end_date_unix'] = $new_end_date_unix;

				echo $courier['name'] . ' нашел регион ' . $regions[$free_regions[$random_region]['real_key']]['name'] . '. ' . ($rotated ? 'Едет по ротации. ' : '') . 'Будет в пути с '. date('Y/m/d H:i', $couriers[$courier_key]['start_date_unix']) . ' до ' . date('Y/m/d H:i', $couriers[$courier_key]['end_date_unix']) . '<br />';
				
				ob_end_flush();

				$city_finded = true;
			}
			else
			{
				$new_end_date_unix = $closest_date_unix;
				$rotation[] = $courier_key;
			}
		}
		else
		{
			echo $courier['name'] . ' занят. Еще в пути ' . date('Y/m/d H:i', $courier['end_date_unix']) . '<br />';
			ob_end_flush();
			//die('Уже работает');

			// Записываем, сколько осталось времени работяге
			$new_end_date_unix = $courier['end_date_unix'];
		}

		// Узнаем возможную ближайшую дату, когда мы сможем отправить следующего
		// Если дата меньше, чем мы думали вначале, тогда будем считать, что это новая дата, когда мы сможем отправить кого-то
		if ($new_end_date_unix < $closest_date_unix)
		{
			//echo 'Closest updated ' . date('Y/m/d', $new_end_date_unix) . ' < ' . date('Y/m/d', $closest_date_unix) . '<br />';
			$closest_date_unix = $new_end_date_unix;
		}
		else
		{
			//echo 'Noo <br />';
		}

		//echo $new_end_date_unix . ' < ' . $closest_date_unix . '<br />';
		//ob_end_flush();
	}

	echo '<br />';

	// На текущий момент все курьеры заняты и теперь мы можем перемотать время к ближайшему свободному + 1 секунду, чтобы работа считалась законченой
	$current_date_unix = $closest_date_unix + 1;

	//echo date('Y/m/d', $current_date_unix) . '<br />';
	//ob_end_flush();

	// Но, если мы уже перемотали до текущего времени, то можно остановиться и посмотреть на работу
	if ($current_date_unix >= $end_date_unix)
	{
		// Останавливаем тайм-менеджмент, выводим сообщение и завершаем работу
		$timemanager_enabled = false;
		echo 'График менеджеров заполнен, оцените работу!';
		break;
	}

	//sleep(30);
}

function printRegions() {
    global $db;
    $regions = $db->get('regions');
    //$regions = $db->get("couries");
    if ($db->count == 0) {
        echo "<td align=center colspan=4>Регионов не найдено</td>";
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

function printEvents() {
    global $db;
    $regions = $db->get("regions");
    foreach ($regions as $region) {
        $region_array[$region['id']] = $region['name'];
    }
    $couriers = $db->get("сouriers");
    foreach ($couriers as $courier) {
        $courier_array[$courier['id']] = $courier['name'];
    }
    $events = $db->get('events', 100);
    if ($db->count == 0) {
        echo "<td align=center colspan=4>Событый не найдено</td>";
        return;
    }
    foreach ($events as $u) {
        $date_start = date('Y/m/d H:i', $u['start_date_unix']);
        $date_end = date('Y/m/d H:i', $u['end_date_unix']);
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
    $сouriers = $db->get('сouriers');
    //$сouriers = $db->get("couries");
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
                    <table width='50%'>
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