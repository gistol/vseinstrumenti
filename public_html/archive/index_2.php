<?php //require_once 'database.class.php';
?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <title>Новый сайт успешно создан и готов к работе</title>
        <!-- b0a8e2d8ccb04b24683d347076e80d29e451a385:d3aa2e6571e673001cb012eda23bd97d02234f0b -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="/datepicker/css/bootstrap-datepicker.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        <script src="/datepicker/js/bootstrap-datepicker.js" ></script>



    </head>
    <body>
        <?php
        $link = mysqli_connect("localhost", "torick_instrum", "Wt&X%x4P", "torick_instrum");

        if (mysqli_connect_errno()) {
            printf("Соединение не удалось: %s\n", mysqli_connect_error());
            exit();
        }

        $query_couriers = "SELECT * FROM сouriers";
        $query_regions = "SELECT * FROM regions";

        if ($result = mysqli_query($link, $query_couriers)) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data_couriers[] = $row;
                $сouriers_array[$row['id']]['id'] = $row['id'];
                $сouriers_array[$row['id']]['name'] = $row['name'];
            }

            mysqli_free_result($result);
        }
        if ($result = mysqli_query($link, $query_regions)) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data_regions[] = $row;
                $regions_array[$row['id']]['id'] = $row['id'];
                $regions_array[$row['id']]['name'] = $row['name'];
                $regions_array[$row['id']]['days'] = $row['days'];
            }

            mysqli_free_result($result);
        }

        mysqli_close($link);


        // Параметры соединения с базой данных
        define('DB_SERVER', 'localhost');       // IP адрес сервера БД или если локальный ПК localhost
        define('DB_USERNAME', 'torick_instrum');         // Имя пользователя
        define('DB_PASSWORD', 'Wt&X%x4P'); // Пароль пользователя
        define('DB_DATABASE', 'torick_instrum');        // Имя базы данных
// Загружаем соединение с базой данных
// Соединение с базой данных
      /*  DataBase::Connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
// Выполняет запрос к базе данных
        $result = mysql_query("SELECT * FROM сouriers");

// Обрабатывает ряд результата запроса и возвращает ассоциативный массив
        $row = mysql_fetch_assoc($result);

// Выводит версию сервера MySQL
        // echo $row['VERSION'];
// Закрываем соединение с базой данных
        DataBase::Close();*/


        //Проверяем что же нам отдала база данных, если null – то какие-то проблемы:
        echo "<pre>";
        var_dump($data_couriers);
        echo "</pre>";

        echo "<pre>";
        var_dump($data_regions);
        echo "</pre>";
        // print_r($сouriers_array);
        print_r($regions_array);

        $startDate = new \DateTime('2018-01-25');
        $endDate = new \DateTime();

        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod($startDate, $interval, $endDate);
        $date = new DateTime('2017-01-17');
        $date->modify('+1 day');

        //echo $date->format('Y-m-d'); // 2017-01-18

        $dates = array();
        foreach ($period as $key => $date) {
            $date_depart = $date->format('Y-m-d');
            // $date_arrive = new \DateTime("Y-m-d", strtotime($date . "+5 days"));
            // $date_arrive = strtotime("+" . $region['date'] . " days", strtotime($date));
            $date_arrive = $date->modify('+1 day');
            $date_comeback = $date->modify('+5 day');
            $date_arrive = $date_arrive->format('Y-m-d'); // 2017-01-18
            $date_comeback = $date_comeback->format('Y-m-d'); // 2017-01-18
            //$date_arrive = strtotime("+5 days", strtot
            //ime($date));
            //echo date("Y-m-d", $date_arrive);
            //$date_arrive = $date->modify('+1 week');
            //$date_arrive = $date_depart('+1 day')->format('Y-m-d');
            //$date_arrive = strtotime("+" . $region['date'] . " days", strtotime($date_depart));
            // $date_comeback = strtotime("+" . $region['date'] . " days", strtotime($date_arrive));
            echo $date_depart . " date_depart <br>";
            echo $date_arrive . " date_arrive <br>";
            echo $date_comeback . " date_comeback <br>";
            //$dates[$date_depart][] = $region['name'];
            //$dates[$date_arrive][] = $region['name'];
            //$dates[$date_comeback][] = $region['name'];
            foreach ($regions_array as $regionid) {
                foreach ($regionid as $region) {
                    
                }
            }
        }
        echo "<pre>";
        //print_r($dates);


        foreach ($dates as $date_key => $date) {
            $schedule_array[$date_key]['Владимир'] = "Станислав";
        }

        echo "<pre>";
        //print_r($schedule_array);
        // Create connection
        $conn = new mysqli('localhost', 'torick_instrum', 'Wt&X%x4P', 'torick_instrum');
// Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        //$sql = "INSERT INTO schedule (regionid, courierid, depart, arrive) VALUES (1, 6, '2018-01-20','2018-01-21')";

        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $conn->close();
        ?>

        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Регион</th>
                                <th scope="col">Дата выезда из Москвы</th>
                                <th scope="col">ФИО курьера</th>
                                <th scope="col">Дата прибытия в регион</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">1</th>
                                <td>Владимир</td>
                                <td>01.06.2015</td>
                                <td>Владимир Иванович</td>
                                <td>02.06.2015</td>
                            </tr>
                            <tr>
                                <th scope="row">2</th>
                                <td>Нижний Новгород</td>
                                <td>01.06.2015</td>
                                <td>Станислав Олегович</td>
                                <td>02.06.2015</td>
                            </tr>
                        </tbody>
                    </table>
                </div><!-- end col -->

            </div>

            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="contact-form">
                        <form id="contact-form" method="post" action="contact.php">
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