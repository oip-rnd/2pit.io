<?php
/**
 * PpitCore V1.0 (https://github.com/p-pit/PpitCore)
 *
 * @link      https://github.com/p-pit/PpitCore
 * @copyright Copyright (c) 2016 Bruno Lartillot
 * @license   https://github.com/p-pit/PpitCore/blob/master/license.txt GNU-GPL license
 */

namespace PpitCore\ViewHelper;

use PpitCore\Model\Context;

class EventPlanningViewHelper
{
    /**
     * Apply a given list of weekly recurrent events to the current week, according to the day of week.
     * @param Event[]
     * @return Event[]
     */
    public static function format($description, $events, $viewBeginDate = null)
    {
    	$context = Context::getCurrent();
    	if (!$viewBeginDate) $viewBeginDate = date('Y-m-d');
    	$dayOfWeek = date('w', strtotime(date('Y-m-d')));
    	if ($dayOfWeek == 0) $dayOfWeek = 7;
    	$week = array();
    	for ($i = 0; $i < 7; $i++) {
    		if ($dayOfWeek > $i) $week[] = date('Y-m-d', strtotime($viewBeginDate.'- '.($dayOfWeek - $i).' days'));
    		else $week[] = date('Y-m-d', strtotime($viewBeginDate.'+ '.($i - $dayOfWeek).' days'));
    	}
    	foreach ($events as &$event) {
			foreach ($event->exception_dates as $exception) if ($exception == date('Y-m-d')) $keep = false;
    		if ($event->properties['begin_date'] <= (($event->properties['day_of_week']) ? $week[$event->properties['day_of_week']] : $week[6]) && (!$event->properties['end_date'] || $event->properties['end_date'] >= (($event->properties['day_of_week']) ? $week[$event->properties['day_of_week']] : $week[0]))) {
    			if ($event->properties['day_of_week']) {
					$keep = true;
    				foreach ($event->exception_dates as $exception) if ($exception == $week[$event->properties['day_of_week']]) $keep = false;
    				if ($keep) {
	    				$event->properties['begin_date'] = $week[$event->properties['day_of_week']];
	    				$event->properties['end_date'] = $week[$event->properties['day_of_week']];
    				}
    				else {
    					$event->properties['begin_date'] = 0;
    					$event->properties['end_date'] = 0;
    				}
    			}
    			else {
    				if ($event->properties['begin_date'] <= $week[0]) $event->properties['begin_date'] = $week[0];
    				if (!$event->properties['end_date'] || $event->properties['end_date'] >= $week[6]) $event->properties['end_date'] = $week[6];
    			}
    		}
    	    else {
    			$event->properties['begin_date'] = 0;
    			$event->properties['end_date'] = 0;
    	    }

    		foreach($description['properties'] as $propertyId => $property) {
    			if ($propertyId != 'type') if ($property['type'] == 'select' && $event->properties[$propertyId] && array_key_exists('modalities', $property)) $event->properties[$propertyId] = $context->localize($property['modalities'][$event->properties[$propertyId]]);
    		}
		}
    	return $events;
    }

    public static function displayPlanning($description, $events, $viewBeginDate, $viewEndDate)
    {
    	$context = Context::getCurrent();

    	// Days is a table of all the date ofthe visible period (ie month, week or single day) associated with the day of week
    	$days = array();
    	for($date = new \DateTime($viewBeginDate); $date <= new \DateTime($viewEndDate); $date->modify('+1 day')) {
    		$days[$date->format('Y-m-d')] = $date->format('w');
    	}
    	$content = array();
		foreach ($events as &$event) {
    		if ($event->begin_date <= ($viewEndDate) && ($event->end_date >= $viewBeginDate)) {
    			foreach ($days as $date => $dayOfWeek) {
    				if (!in_array($date, $event->exception_dates) && $event->begin_date <= $date && $event->end_date >= $date) {
	    				if ($event->begin_date == $date || $event->day_of_week == $dayOfWeek || $event->day_of_month == substr($date, 5, 2)) {
	    					$content[] = array(
	    						'begin_date' => $date,
	    						'end_date' => $date,
	    						'begin_time' => $event->begin_time,
	    						'end_time' => $event->end_time,
	    						'caption' => $event->caption,
	    						'location' => $event->location,
	    					);
	    				}
    				}
    			}
    		}

    		foreach($description['properties'] as $propertyId => $property) {
    			if ($property['type'] == 'select' && $event->properties[$propertyId] && array_key_exists('modalities', $property)) $event->properties[$propertyId] = $context->localize($property['modalities'][$event->properties[$propertyId]]);
    		}
		}
    	return $content;
    }

    public static function displayAvailability($accounts, $viewBeginDate, $viewEndDate)
    {
    	$context = Context::getCurrent();
		$dayOfWeeks = [0 => 'sunday', 1 => 'monday', 2 => 'tuesday', 3 => 'wednesday', 4 => 'thursday', 5 => 'friday', 6 => 'saturday'];
		$days = array();
		for($date = new \DateTime($viewBeginDate); $date <= new \DateTime($viewEndDate); $date->modify('+1 day')){
			$dayOfWeek = $dayOfWeeks[$date->format('w')];
			$day = array(
				'date' => $date->format('Y-m-d'),
				'dayOfWeek' => $dayOfWeek,
			);
			foreach ($accounts as $account) {
				foreach ($account['availability_exceptions'] as $exception) {
					if (!array_key_exists('end_date', $exception)) $exception['end_date'] = $exception['begin_date'];
					if ($exception['begin_date'] <= $day['date'] && $exception['end_date'] >= $day['date']) {
						$day['color']['morning'] = 'red';
						$day['color']['afternoon'] = 'red';
						$day['color']['evening'] = 'red';
					}
				}
				if ($account['availability_begin'] <= $day['date'] && $account['availability_end'] >= $day['date']) {
					$constraints = $account['availability_constraints'][0];
					if (array_key_exists($dayOfWeek, $constraints)) {
						if ($constraints[$dayOfWeek] == 'morning' || $constraints[$dayOfWeek] == 'day') {
							if (!array_key_exists('color', $day) || !array_key_exists('morning', $day['color'])) {
								$day['color']['morning'] = 'Green';
							}
						}
						if ($constraints[$dayOfWeek] == 'afternoon' || $constraints[$dayOfWeek] == 'day') {
							if (!array_key_exists('color', $day) || !array_key_exists('afternoon', $day['color'])) {
								$day['color']['afternoon'] = 'Green';
							}
						}
						if ($constraints[$dayOfWeek] == 'evening') {
							if (!array_key_exists('color', $day) || !array_key_exists('evening', $day['color'])) {
								$day['color']['evening'] = 'Green';
							}
						}
					}
				}
			}
			$days[] = $day;
    	}
    	return $days;
    }
}