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

    	// Days is a table of all the date of the visible period (ie month, week or single day) associated with the day of week
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
	    					$captionFormat = $context->getConfig('event/format/' . $event->type);
	    					if (!$captionFormat) $captionFormat = $context->getConfig('event/format/generic');
							$arguments = array();
							foreach ($captionFormat['params'] as $parameterId => $options) {
								$parameter = $description['properties'][$parameterId];
								if ($parameter['type'] == 'select' && $event->properties[$parameterId]) {
									$value = $context->localize($parameter['modalities'][$event->properties[$parameterId]]);
								}
								else $value = $event->properties[$parameterId];
								$arguments[] = $value;
							}
							$formatted = vsprintf($captionFormat['mask'], $arguments);
	    					$content[] = array(
	    						'id' => $event->id,
	    						'category' => $event->category,
	    						'begin_date' => $date,
	    						'end_date' => $date,
	    						'begin_time' => $event->begin_time,
	    						'end_time' => $event->end_time,
	    						'caption' => $event->caption,
	    						'location' => $event->location,
	    						'formatted' => $formatted,
	    						'account_id' => $event->account_id,
	    					);
	    				}
    				}
    			}
    		}

    		foreach($description['properties'] as $propertyId => $property) {
    			if ($property['type'] == 'select' && $event->properties[$propertyId] && array_key_exists($event->properties[$propertyId], $property['modalities'])) {
    				$event->properties[$propertyId] = $context->localize($property['modalities'][$event->properties[$propertyId]]);
    			}
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
						$day['n_fn'] = $account['n_fn'];
					}
				}
				if ($account['availability_begin'] <= $day['date'] && (!$account['availability_end'] || $account['availability_end'] >= $day['date'])) {
					$constraints = $account['availability_constraints'][0];
					if (array_key_exists($dayOfWeek, $constraints)) {
						if ($constraints[$dayOfWeek] == 'morning' || $constraints[$dayOfWeek] == 'day') {
							if (!array_key_exists('color', $day) || !array_key_exists('morning', $day['color'])) {
								$day['color']['morning'] = 'Green';
								$day['n_fn'] = $account['n_fn'];
							}
						}
						if ($constraints[$dayOfWeek] == 'afternoon' || $constraints[$dayOfWeek] == 'day') {
							if (!array_key_exists('color', $day) || !array_key_exists('afternoon', $day['color'])) {
								$day['color']['afternoon'] = 'Green';
								$day['n_fn'] = $account['n_fn'];
							}
						}
						if ($constraints[$dayOfWeek] == 'evening') {
							if (!array_key_exists('color', $day) || !array_key_exists('evening', $day['color'])) {
								$day['color']['evening'] = 'Green';
								$day['n_fn'] = $account['n_fn'];
							}
						}
					}
				}
			}
			$days[] = $day;
    	}
    	return $days;
    }

    public static function displayMap($type, $viewBeginDate, $viewEndDate)
    {
    	$context = Context::getCurrent();
		$dayOfWeeks = [0 => 'sunday', 1 => 'monday', 2 => 'tuesday', 3 => 'wednesday', 4 => 'thursday', 5 => 'friday', 6 => 'saturday'];
		$days = array();
    	$planningMap = $context->getConfig('planningMap/' . $type);
    	if (!$planningMap) $planningMap = $context->getConfig('planningMap/generic');
    	foreach ($planningMap['periods'] as $period) {
			$mapBegin = $period['begin'];
			$mapEnd = $period['end'];
			$mapExceptions = $period['exceptions'];
			$mapSlots = $period['slots'];
		
			for($date = new \DateTime($viewBeginDate); $date <= new \DateTime($viewEndDate); $date->modify('+1 day')){
				$dayNumber = $date->format('w');
				$dayOfWeek = $dayOfWeeks[$dayNumber];
				$day = array(
					'date' => $date->format('Y-m-d'),
					'dayOfWeek' => $dayOfWeek,
					'slots' => [],
				);
				$ignore = false;
				foreach ($mapExceptions as $exception) {
					if (!array_key_exists('end_date', $exception)) $exception['end_date'] = $exception['begin_date'];
					if ($exception['begin_date'] <= $day['date'] && $exception['end_date'] >= $day['date']) {
						$ignore = true;
					}
				}
				if (!$ignore) {
					if ($mapBegin <= $day['date'] && (!$mapEnd || $mapEnd >= $day['date'])) {
						if (array_key_exists($dayNumber, $mapSlots)) {
							foreach ($mapSlots[$dayNumber] as $slot) $day['slots'][] = $slot;
						}
					}
				}
				$days[] = $day;
			}
    	}
    	return $days;
    }
/*
    public static function displayConcurrencies($description, $category, $accounts, $events, $viewBeginDate, $viewEndDate)
    {
    	$context = Context::getCurrent();
    
    	// Days is a table of all the date of the visible period (ie month, week or single day) associated with the day of week
    	$days = array();
    	for($date = new \DateTime($viewBeginDate); $date <= new \DateTime($viewEndDate); $date->modify('+1 day')) {
    		$days[$date->format('Y-m-d')] = $date->format('w');
    	}
    	$content = [];

    	foreach ($events as $event) {
    		if (array_key_exists($event->account_id, $accounts) && $event->category != $category) {
				if ($event->begin_date <= ($viewEndDate) && ($event->end_date >= $viewBeginDate)) {
	    			foreach ($days as $date => $dayOfWeek) {
	    				if (!in_array($date, $event->exception_dates) && $event->begin_date <= $date && $event->end_date >= $date) {
		    				if ($event->begin_date == $date || $event->day_of_week == $dayOfWeek || $event->day_of_month == substr($date, 5, 2)) {
		    					$captionFormat = $context->getConfig('event/format/' . $event->type);
		    					if (!$captionFormat) $captionFormat = $context->getConfig('event/format/generic');
								$arguments = array();
								foreach ($captionFormat['params'] as $parameterId => $options) {
									$parameter = $description['properties'][$parameterId];
									if ($parameter['type'] == 'select' && $event->properties[$parameterId]) {
										$value = $context->localize($parameter['modalities'][$event->properties[$parameterId]]);
									}
									else $value = $event->properties[$parameterId];
									$arguments[] = $value;
								}
								$formatted = vsprintf($captionFormat['mask'], $arguments);
		    					$content[] = array(
		    						'id' => $event->id,
		    						'begin_date' => $date,
		    						'end_date' => $date,
		    						'begin_time' => $event->begin_time,
		    						'end_time' => $event->end_time,
		    						'caption' => $event->caption,
		    						'location' => $event->location,
		    						'n_fn' => $event->n_fn,
		    						'account_id' => $event->account_id,
		    					);
		    				}
	    				}
	    			}
	    		}
    		}
    	}
    	 
		return $content;
    }*/
}