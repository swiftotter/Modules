<?php
/**
 * SwiftOtter_Base is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SwiftOtter_Base is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with SwiftOtter_Base. If not, see <http://www.gnu.org/licenses/>.
 *
 * Copyright: 2013 (c) SwiftOtter Studios
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 3/14/13
 * @package default
 **/

/**
 * Class SwiftOtter_Base_Helper_Data
 */
class SwiftOtter_Base_Helper_Data extends Mage_Core_Helper_Data
{
    protected $_ranges;
    protected $_offset;
    protected $_dateRange;

    const DEFAULT_RANGE = 'today';

	protected $_section = 'swiftotter_config';
	protected $_group = 'abstract';

	public function getStoreConfig($name, $group = null)
	{
		$path = $this->_section . DS;
		if (!$group) {
			$path .= $this->_group . DS;
		} else {
			$path .= $group . DS;
		}
		$path .= $name;

		return Mage::getStoreConfig($path);
	}

	public function getStoreConfigFlag($name, $group = null)
	{
		$path = $this->_section . DS;
		if (!$group) {
			$path .= $this->_group . DS;
		} else {
			$path .= $group . DS;
		}
		$path .= $name;

		return Mage::getStoreConfigFlag($path);
	}


    public function toUnderscore($input)
    {
        return strtolower(preg_replace('/([A-Z])/', '_$1', $input));
    }

    public function toUnderscoreUrl($input)
    {
        return strtolower(trim(str_replace(' ', '_', $input), ' '));
    }

    public function toCamelCase($input)
    {
        return preg_replace("/\_(.)/e", "strtoupper('\\1')", $input);
    }

    /**
     * @param $registryNode
     * @return Varien_Object
     */
    public function getDateRange($registryNode)
    {
        if (!$this->_dateRange) {
            $this->initDateFilterParams($registryNode);
        }

        return $this->_dateRange;
    }

    /**
     * Initializes the filter params Varien_Object, and saves it to the session
     *
     * @param $registryNode
     * @return Varien_Object
     */
    public function initDateFilterParams($registryNode)
    {
        $request = Mage::app()->getRequest();

        if (!$request->getParam('form_filter')) {
            $input = Mage::getSingleton('adminhtml/session')->getData($registryNode);

            if (!$input) {
                $input = new Varien_Object();
                $input->setStandard($this->_getDefaultRange());
            }
        } else {
            $input = new Varien_Object(get_object_vars(
                json_decode($request->getParam('form_filter'))
            ));
        }

        $start = null;
        $end = null;

        if ($input->getStandard()) {
            $range = $this->getRange($input->getStandard());
        } else {
            $timezone = $this->getTimezone();
            $range = new SwiftOtter_Base_Model_DateRange('Custom Range', 'custom', $input->getStart(), $input->getEnd(), $timezone);
        }

        $input->setRange($range);

        $this->_dateRange = $input;

        Mage::getSingleton('adminhtml/session')->setData($registryNode, $input);

        return $input;
    }

	/**
	 *
	 *
	 * @param null $registryKey
	 * @return string
	 */
	public function getDateFilterParams($registryKey = null)
    {
        $request = Mage::app()->getRequest();
        if ($request->getParam('form_filter')) {
            return $request->getParam('form_filter');
        } else {
            if ($registryKey) {
                return json_encode(
                    $this->getDateRange($registryKey)->getData(), 0, 1
                );
            } else {
                return json_encode(
                    array('standard' => $this->_getDefaultRange())
                );
            }
        }
    }

    protected function _getDefaultRange()
    {
        return self::DEFAULT_RANGE;
    }

	/**
	 * Creates a standard list of date ranges to use
	 *
	 * @return array
	 */
	public function getDateRanges()
    {
        if (!$this->_ranges) {
            $timezone = new DateTimeZone($this->getTimezone());

            $time = new DateTime('now', $timezone);
            $day = new DateTime(date('Y-m-d', $time->getTimestamp()), $timezone);

            $month = new DateTime(date('Y-m-1', $day->getTimestamp()), $timezone);
            $year = new DateTime(date('Y-1-1', $day->getTimestamp()), $timezone);

            $fullDay = $this->_getDate($day, 'P1D');

            $output = array(
                new SwiftOtter_Base_Model_DateRange('today', 'Today', $day, $fullDay),
                new SwiftOtter_Base_Model_DateRange('yesterday', 'Yesterday', $this->_getDate($day, '-P1D'), $day),
                new SwiftOtter_Base_Model_DateRange('two_days_ago', '2 Days Ago', $this->_getDate($day, '-P2D'), $this->_getDate($day, '-P1D')),
                new SwiftOtter_Base_Model_DateRange('week_to_date', 'Week to Date', $this->_getDate($day, '-P7D'), $fullDay),

                new SwiftOtter_Base_Model_DateRange('month_to_date', 'Month to Date', $this->_getDate($day, '-P1M'), $fullDay),
                new SwiftOtter_Base_Model_DateRange('month', 'Current Month', $month, $this->_getDate($month, 'P1M')),

                new SwiftOtter_Base_Model_DateRange('year_to_date', 'Year to Date', $this->_getDate($day, '-P1Y'), $fullDay),
                new SwiftOtter_Base_Model_DateRange('year', 'Current Year', $year, $this->_getDate($year, 'P1Y')),

                new SwiftOtter_Base_Model_DateRange('last_year_to_date', 'Last Year to Date', $this->_getDate($day, '-P2Y'), $this->_getDate($day, '-P1Y')),
                new SwiftOtter_Base_Model_DateRange('last_year', 'Last Year', $this->_getDate($year, '-P1Y'), $year),

                new SwiftOtter_Base_Model_DateRange('last_2_days', 'Last 2 Days', $this->_getDate($day, '-P2D'), $fullDay),
                new SwiftOtter_Base_Model_DateRange('last_2_weeks', 'Last 2 Weeks', $this->_getDate($day, '-P2W'), $fullDay),
                new SwiftOtter_Base_Model_DateRange('last_3_weeks', 'Last 3 Weeks', $this->_getDate($day, '-P2W'), $fullDay),
                new SwiftOtter_Base_Model_DateRange('last_2_months', 'Last 2 Months', $this->_getDate($day, '-P2M'), $fullDay),
                new SwiftOtter_Base_Model_DateRange('last_3_months', 'Last 3 Months', $this->_getDate($day, '-P3M'), $fullDay)
            );

            $this->_ranges = $output;
        }

        return $this->_ranges;
    }



    /**
     * Returns the specific unformatted date range.
     *
     * @param $range
     * @return array
     */
    public function getRange($range)
    {
        $dateRanges = $this->getDateRanges();

        /** @var SwiftOtter_Base_Model_DateRange $dateRange */
        foreach ($dateRanges as $dateRange) {
            if ($dateRange->getIdentifier() == $range) {
                return $dateRange;
            }
        }

        return new SwiftOtter_Base_Model_DateRange('invalid');
    }

    /**
     * A wrapper for the \DateTime->add and \DateTime->sub functions to speed up calculations and cloning.
     *
     * @param $input
     * @param $adjustment
     * @return DateTime
     */
    protected function _getDate($input, $adjustment)
    {
        $date = clone $input;
        $add = strpos($adjustment, '-') === false;

        if ($add) {
            return $date->add(new DateInterval($adjustment));
        } else {
            $adjustment = substr($adjustment, 1);
            return $date->sub(new DateInterval($adjustment));
        }
    }

    /**
     * Gets the current store timezone
     *
     * @return string
     */
    public function getTimezone()
    {
        return Mage::app()->getStore()->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);
    }

    /**
     * Calculates the offset/time difference between the user-views time zone and UTC (system default)
     *
     * @return integer
     */
    public function getOffset()
    {
        if (!$this->_offset) {
            $timezoneName = $this->getTimezone();

            $timezone = new DateTimeZone($timezoneName);
            $time = new DateTime();

            $gmtDay = (int)$time->format('d');
            $localDay = (int)date('d', Mage::getModel('core/date')->timestamp(now()));

            $this->_offset = $gmtDay - $localDay;
        }

        return $this->_offset;
    }

    /**
     * Formats the date ranges calculated above into a human-readable, time-zone adjusted value.
     *
     * @return array
     */
    public function getFormattedDateRanges()
    {
        $output = array();
        /** @var SwiftOtter_Base_Model_DateRange $range */
        foreach ($this->getDateRanges() as $range) {
            $output[$range->getIdentifier()] = $range->getArray();
        }

        return $output;
    }


}