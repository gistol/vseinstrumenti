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
        //Устанавливаем доступы к базе данных:
        $host = 'localhost'; //имя хоста, на локальном компьютере это localhost
        $user = 'torick_instrum'; //имя пользователя, по умолчанию это root
        $password = 'Wt&X%x4P'; //пароль, по умолчанию пустой
        $db_name = 'torick_instrum'; //имя базы данных
        //Соединяемся с базой данных используя наши доступы:

        $link = mysqli_connect($host, $user, $password, $db_name);
        mysqli_connect($host, $user, $password, $db_name) or die(mysqli_error($link));
        //Устанавливаем кодировку (не обязательно, но поможет избежать проблем):
        //mysqli_query($link, "SET NAMES 'utf8'");
        //Формируем тестовый запрос:
        $query_couriers = "SELECT * FROM сouriers";
        $query_regions = "SELECT * FROM regions";
        //Делаем запрос к БД, результат запроса пишем в $result:
        $result_couriers = mysqli_query($link, $query_couriers) or die(mysqli_error($link));
        $result_regions = mysqli_query($link, $query_regions) or die(mysqli_error($link));
        for ($data_couriers = []; $row_couriers = mysqli_fetch_assoc($result_couriers); $data_couriers[] = $row_couriers)
            ;
        for ($data_regions = []; $row_regions = mysqli_fetch_assoc($result_regions); $data_regions[] = $row_regions)
            ;

        $mysqli = new mysqli('localhost', 'torick_instrum', 'Wt&X%x4P', 'torick_instrum');
        if (mysqli_connect_errno()) {
            echo "Подключение невозможно: " . mysqli_connect_error();
        }
        $result_set_сouriers = $mysqli->query('SELECT * FROM сouriers');
        $result_set_сouriers->num_rows;
        while ($row = $result_set_сouriers->fetch_assoc()) {
            //print_r($row);
            //echo "<br />";
            $сouriers_array[$row['id']]['id'] = $row['id'];
            $сouriers_array[$row['id']]['name'] = $row['name'];
        }
        $result_set_сouriers->close();


        $result_set_regions = $mysqli->query('SELECT * FROM regions');
        $result_set_regions->num_rows;
        while ($row = $result_set_regions->fetch_assoc()) {
           // print_r($row);
            //echo "<br />";
            $regions_array[$row['id']]['id'] = $row['id'];
            $regions_array[$row['id']]['name'] = $row['name'];
            $regions_array[$row['id']]['days'] = $row['days'];
        }
        $result_set_regions->close();
        $mysqli->close();


        //Проверяем что же нам отдала база данных, если null – то какие-то проблемы:
        // echo "<pre>";
        echo "<pre>";
        var_dump($data_couriers);
        echo "</pre>";
        
        echo "<pre>";
        var_dump($data_regions);
        echo "</pre>";
        // print_r($сouriers_array);
        // print_r($regions_array);

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
            $date_comeback = $date->modify('+2 day');
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
                            <tr>
                                <th scope="row">3</th>
                                <td>Larry</td>
                                <td>the Bird</td>
                                <td>@twitter</td>
                                <td>@twitter</td>
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
            <div class="row">
                <div class="col-md-12">
                    <p>Enter your first name, last name, and email to be added to the <strong>Make Me Elvis</strong> mailing list.</p>

                    <?php
                    if (isset($_POST['submit'])) {
                        $first_name = $_POST['firstname'];
                        $last_name = $_POST['lastname'];
                        $email = $_POST['email'];
                        $output_form = 'no';

                        if (empty($first_name) || empty($last_name) || empty($email)) {
                            // We know at least one of the input fields is blank 
                            echo 'Please fill out all of the email information.<br />';
                            $output_form = 'yes';
                        }
                    } else {
                        $output_form = 'yes';
                    }

                    if (!empty($first_name) && !empty($last_name) && !empty($email)) {
                        $dbc = mysqli_connect('data.makemeelvis.com', 'elmer', 'theking', 'elvis_store')
                                or die('Error connecting to MySQL server.');

                        $query = "INSERT INTO email_list (first_name, last_name, email)  VALUES ('$first_name', '$last_name', '$email')";
                        mysqli_query($dbc, $query)
                                or die('Data not inserted.');

                        echo 'Customer added.';

                        mysqli_close($dbc);
                    }

                    if ($output_form == 'yes') {
                        ?>

                        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <label for="firstname">First name:</label>
                            <input type="text" id="firstname" name="firstname" /><br />
                            <label for="lastname">Last name:</label>
                            <input type="text" id="lastname" name="lastname" /><br />
                            <label for="email">Email:</label>
                            <input type="text" id="email" name="email" /><br />
                            <input type="submit" name="submit" value="Submit" />
                        </form>

                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>


    </body>
</html>