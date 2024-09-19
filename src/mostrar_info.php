<?php

include 'database.php';

// Obtener el número total de habitaciones
$result = $database->getNumHabitacionesTotal();
$totalHabitaciones = $result->fetch_assoc()['total'];

// Obtener el número de habitaciones libres
$result = $database->getNumHabitacionesLibres();
$habitacionesLibres = $result->fetch_assoc()['libres'];

// Obtener la capacidad total del hotel
$capacidadTotal = $database->getCapacidadTotalPorHabitaciones();

// Obtener el número de huéspedes alojados
$result = $database->getHuespedesTotales();
$huéspedesAlojados = $result->fetch_assoc()['alojados'];

// Crear un array con la información del hotel
$infoHotel = [
    'totalHabitaciones' => $totalHabitaciones,
    'habitacionesLibres' => $habitacionesLibres,
    'capacidadTotal' => $capacidadTotal,
    'huéspedesAlojados' => $huéspedesAlojados
];



