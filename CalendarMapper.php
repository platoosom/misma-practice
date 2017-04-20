<?php

namespace Appointment;

require "Models/Appointment.php";
require "Models/User.php";
require "Calendar.php";

use Appointment\Calendar;
use Appointment\Models\Appointment;
use Appointment\Models\User;
use PDO;

class CalendarMapper
{
    public static function init()
    {
        try {
            // Connect to database
            $conn = self::pdoConnect();

            // Create user table
            $userSql = "
                  CREATE TABLE IF NOT EXISTS `user` (
                    `id` INT NOT NULL auto_increment,
                    `name` VARCHAR(50) NOT NULL,
                    `email` VARCHAR(255) NOT NULL,
                    PRIMARY KEY (`id`)
                  ) CHARACTER SET utf8 COLLATE utf8_general_ci
                  ";
            $conn->exec($userSql);

            // Create appointment table
            $appointmentSql = "
                  CREATE TABLE IF NOT EXISTS `appointment` (
                    `id` INT NOT NULL auto_increment,
                    `date` DATE NOT NULL,
                    `user_id` INT NOT NULL,
                    PRIMARY KEY (`id`),
                    FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) 
                  ) CHARACTER SET utf8 COLLATE utf8_general_ci
                  ";
            $conn->exec($appointmentSql);

            $conn = null;

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function __construct()
    {
        self::init();
    }

    public static function save(Appointment $appointment)
    {
        try {
            // Connect to database.
            $conn = self::pdoConnect();

            // Insert user
            $userStatement = $conn->prepare("INSERT INTO user(name, email) VALUES (:name, :email)");
            $userStatement->execute(array(
                "name" => $appointment->getUser()->getName(),
                "email" => $appointment->getUser()->getEmail(),
            ));
            $user_id = $conn->lastInsertId();

            // Insert appointment
            $appointmentStatement = $conn->prepare("INSERT INTO appointment(date, user_id) VALUES (:date, :user_id)");
            $appointmentStatement->execute(array(
                "date" => $appointment->getDate(),
                "user_id" => $user_id,
            ));

            $conn = null;

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function load($startDate, $endDate)
    {
        $calendar = new Calendar($startDate, $endDate);

        try {
            // Connect to database.
            $conn = self::pdoConnect();

            $sql = "SELECT * FROM  user INNER JOIN appointment ON user.id = appointment.user_id WHERE appointment.date BETWEEN '".$startDate."' AND '".$endDate."'";
            $appointmentRows = $conn->query($sql);
            foreach($appointmentRows as $appointmentRow){
                $user = new User($appointmentRow['name'], $appointmentRow['email']);
                $appointment = new Appointment($appointmentRow['date'], $user);

                $calendar->addAppointment($appointment);
            }

            $conn = null;
            return $calendar;
        } catch (PDOException $e) {
            return $calendar;
        }
    }

    private function pdoConnect()
    {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "appointment";

        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $conn;
    }
}
