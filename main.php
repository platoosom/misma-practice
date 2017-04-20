<?php

header('Content-Type: application/json');

require "CalendarMapper.php";

use Appointment\CalendarMapper;

switch ($_SERVER['REQUEST_METHOD']){
    case 'GET':

        $start = (!empty($_GET['date']))? $_GET['date']: date('Y-m-01');
        $end = (!empty($_GET['date']))? date('Y-m-d', strtotime('+30 days', strtotime($start))): date('Y-m-t', strtotime($start));

        $calendar = CalendarMapper::load($start, $end);
        $appointmentList = $calendar->getAppointmentLists();
        $appointments = [];

        for($i=strtotime($start); $i<= strtotime($end); $i=strtotime('+1 day', $i)){
            $day = date('Y-m-d', $i);
            if(array_key_exists(date('Y-m-d', $i), $appointmentList)){
                $appointments[$day] = [
                    'name' => $appointmentList[$day]->getUser()->getName(),
                    'email' => $appointmentList[$day]->getUser()->getEmail(),
                ];
            }else{
                $appointments[$day] = null;
            }
        }

        $json = [
            'range' => [
                'start' => $start,
                'end' => $end,
            ],
            'calendar' => $appointments,
        ];

        die(json_encode($json));

        break;
    case 'POST':
        $date = $_POST['date'];
        $name = $_POST['name'];
        $email = $_POST['email'];

        try{
            $user = new \Appointment\Models\User($name, $email);
            $appointment = new \Appointment\Models\Appointment($date, $user);

            CalendarMapper::save($appointment);

            die(json_encode([
                'code' => 200,
                'message' => 'Appointment successfully added',
            ]));

        }catch (Exception $e){
            die(json_encode([
                    'code' => 200,
                    'message' => $e->getMessage(),
            ]));
        }

        break;
}

die(json_encode([
    'code' => 404,
    'message' => 'Not found',
]));




