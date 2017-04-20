<?php

namespace Appointment;

use Appointment\Models\Appointment;
use Exception;

class Calendar
{
    private $startDate;
    private $endDate;
    private $appointmentList;

    public function __construct($startDate, $endDate)
    {
        if(strtotime($startDate) > strtotime($endDate)){
            $this->startDate = $endDate;
            $this->endDate = $startDate;
        }else{
            $this->startDate = $startDate;
            $this->endDate = $endDate;
        }

        $this->appointmentList = [];
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    public function getDiffDays()
    {
        $interval = date_diff(date_create($this->startDate), date_create($this->endDate));
        return $interval->d;
    }

    public function addAppointment(Appointment $appointment)
    {
        $dateNumber = strtotime($appointment->getDate());
        if($dateNumber < strtotime($this->startDate) || $dateNumber > strtotime($this->endDate)){
            throw new Exception('Invalid Appointment');
        }

        //Todo: please edit this in_array cause appointment is object
        if(in_array($appointment, $this->appointmentList)){
            return false;
        }else{
            $this->appointmentList[$appointment->getDate()] = $appointment;
            return true;
        }
    }

    public function getFreeDates()
    {
        $availableDates = [];
        $startDate = strtotime($this->startDate);
        $endDate = strtotime($this->endDate);

        for ($i = $startDate; $i <= $endDate; $i = strtotime('+1 day', $i)){
            $key = date('Y-m-d', $i);

            if(!array_key_exists($key, $this->appointmentList)){
                $availableDates[$key] = '';
            }
        }

        return $availableDates;
    }

    public function getAppointmentLists()
    {
        return $this->appointmentList;
    }

}