    <?php
    try{
        $dbHandler = new PDO("mysql:host=mysql;dbname=gemorskos;charset=utf8", "root", "qwerty");
    }
    catch(Exception $ex){
        echo "The following exception has occurred $ex";
    }

    if (!isset($_GET["event_date"])) {
        $today = date("d");
        header("Location: Calendar.php?event_date=$today");
    }

    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link type="text/css" rel="stylesheet" href="CalendarCSS.css">
        <link type="text/css" rel="stylesheet" href="normalize.css">
        <title>Title</title>

    </head>
    <body>

    <div id="container">
        <header>
        </header>

        <div id="calendar">
            <div id="dateAndEvents">
                <?php
                if($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET["event_date"])) {
                    echo '<div id="date"><span style="font-size: 3em;" id="calendarDate">' . $_GET["event_date"] . '</span><h3>'. date("F") . '<br>' . date("o") . '</h3></div>';
                }
                ?>


                <div class="eventList">
                    <?php
                    if(isset($dbHandler)){
                        try {
                            $currDate = date("o") . "-" . date("m") . "-" . $_GET['event_date'];
                            $stmt = $dbHandler->prepare("SELECT * FROM `Events` WHERE event_date = :event_date");
                            $stmt->bindParam("event_date", $currDate, PDO::PARAM_STR);

                            $stmt->execute();
                            $value = $stmt->fetch(PDO::FETCH_OBJ);
                            if ($stmt->rowCount() > 0) {
                                echo "<br>";
                                echo "<u>Upcoming Events</u>";
                                echo "<ul>" . $value->event_name . "</ul>";
                                echo "<li>" . $value->event_description . "</li>";
                                echo "<li>" . $value->event_time . " "  . $value->event_date . "</li>";
                                echo "<li>" . $value->location_street . " " . $value->location_city . ", " . $value->location_postal_code . "</li>";
                            }
                        }
                        catch(Exception $ex){
                            echo "The following exception has occurred $ex";
                        }
                    }
                    else{
                        echo "No events planned";
                    }
                    ?>
                </div>
            </div>

            <table border='0'>
                <?php
                $date = getdate();

                $mday = $date['mday'];
                $mon = $date['mon'];
                $wday = $date['wday'];
                $month = $date['month'];
                $year = $date['year'];

                $dayCount = $wday;
                $day = $mday;

                while($day > 0) {
                    $days[$day--] = $dayCount--;
                    if($dayCount < 0)
                        $dayCount = 6;
                }

                $dayCount = $wday;
                $day = $mday;

                // checking the last day of the current month
                if(checkdate($mon,31,$year))
                    $lastDay = 31;
                elseif(checkdate($mon,30,$year))
                    $lastDay = 30;
                elseif(checkdate($mon,29,$year))
                    $lastDay = 29;
                elseif(checkdate($mon,28,$year))
                    $lastDay = 28;

                while($day <= $lastDay) {
                    $days[$day++] = $dayCount++;
                    if($dayCount > 6)
                        $dayCount = 0;
                }

                // Days to highlight
                $day_to_highlight = array(8, 9, 10, 11, 12, 22,23,24,25,26);

                echo("<tr>");
                echo("</tr>");
                echo("<tr>");
                echo("<td>S</td>");
                echo("<td>M</td>");
                echo("<td>T</td>");
                echo("<td>W</td>");
                echo("<td>T</td>");
                echo("<td>F</td>");
                echo("<td>S</td>");
                echo("</tr>");

                $startDay = 0;
                $d = $days[1];

                echo("<tr>");
                while($startDay < $d) {
                    echo("<td></td>");
                    $startDay++;
                }

                for ($d=1;$d<=$lastDay;$d++) {
                    if (in_array( $d, $day_to_highlight))
                        $bg = "bg-green";
                    else
                        $bg = "bg-white";
                    // Highlights the detail of day
                    if($d == $mday)
                        echo("<td class='bg-blue'><a href='?event_date=$d' title='Detail of day'>$d</a></td>");
                    else
                        echo("<td class='$bg'><a href='?event_date=$d' title='Detail of day'>$d</a></td>");

                    // Counting week and showing it to users
                    $startDay++;
                    if($startDay > 6 && $d < $lastDay){
                        $startDay = 0;
                        echo("</tr>");
                        echo("<tr>");
                    }
                }
                echo("</tr>");
                // TODO: Welcome user, connect header footer
                ?>
            </table>
        </div>
    </div>
    </body>
    </html>
