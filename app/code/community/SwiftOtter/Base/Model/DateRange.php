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

class SwiftOtter_Base_Model_DateRange extends Varien_Object
{
    protected $_start;
    protected $_end;
    protected $_label;
    protected $_identifier;
    protected $_timezone;

    const TIMEZONE_DEFAULT = 'default';
    const DATE_FORMAT = "%s: %s - %s";

    public function __construct($identifier, $label = '', $start = '', $end = '', $timezone = null)
    {
        if (!$timezone) {
            $timezone = $this->getTimezone();
        }

        $this->setStart($start, $timezone);
        $this->setEnd($end, $timezone);
        $this->_label = $label;
        $this->_identifier = $identifier;
    }

    /**
     * Creates a label in the localized time format of [LABEL]: [START DATE] - [END DATE]
     *
     * @return string
     */
    public function getFormattedLabel()
    {
        $offset = Mage::helper('SwiftOtter_Base')->getOffset();

        $start = (int)Mage::getModel('core/date')->timestamp($this->_start->getTimestamp());
        $end = (int)Mage::getModel('core/date')->timestamp($this->_end->getTimestamp());

        if ($offset) {
            $start += $offset;
            $end += $offset;
        }

        $startOutput = date('m/d/Y', $start);
        $endOutput = date('m/d/Y', $end);

        return sprintf(self::DATE_FORMAT, $this->_label, $startOutput, $endOutput);
    }

    public function getDate($type, $timezone)
    {
        /** @var DateTime $time */
        $time = $this->getData($type);
        $output = clone $time;
        if ($time) {
            $output = $time->setTimezone(new DateTimeZone($timezone));
        }

        return $output;
    }


    /**
     * Formats the publicly viewable portions of the class into an array
     *
     * @return array
     */
    public function getArray()
    {
        $output = array(
            'formatted' => $this->getFormattedLabel(),
            'label' => $this->_label
        );

        return $output;
    }

    /**
     * @param string $identifier
     * @return $this
     */
    public function setIdentifier($identifier)
    {
        $this->_identifier = $identifier;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->_identifier;
    }


    /**
     * Sets the End parameter for this class, and normalizes it to UTC
     *
     * @param $end
     * @param string $timezone
     * @return $this
     */
    public function setEnd($end, $timezone = 'UTC')
    {
        if (!is_object($end)) {
            $end = new DateTime($end, new DateTimeZone($timezone));
        }
        $end->setTimezone(new DateTimeZone($timezone));

        $this->_end = $end;

        return $this;
    }

    /**
     * Returns the end time in the raw, UTC value
     *
     * @return DateTime
     */
    public function getEndUTC()
    {
        return $this->_end;
    }

    /**
     * @param string $timezone
     * @return DateTime
     */
    public function getEnd($timezone = self::TIMEZONE_DEFAULT)
    {
        if ($timezone == self::TIMEZONE_DEFAULT) {
            $timezone = $this->getTimezone();
        }

        /** @var DateTime $output */
        $output = clone $this->_end;
        $output->setTimezone(new DateTimeZone($timezone));

        return $output;
    }

    /**
     * Sets the Start parameter for this class, and normalizes it to UTC
     *
     * @param $start
     * @param string $timezone
     * @return $this
     */
    public function setStart($start, $timezone = 'UTC')
    {
        if (!is_object($start)) {
            $start = new DateTime($start, new DateTimeZone($timezone));
        }
        $start->setTimezone(new DateTimeZone($timezone));

        $this->_start = $start;

        return $this;
    }

    /**
     * Returns the start time in the raw, UTC value
     *
     * @return DateTime
     */
    public function getStartUTC()
    {
        return $this->_start;
    }

    /**
     * @param string $timezone
     * @return DateTime
     */
    public function getStart($timezone = self::TIMEZONE_DEFAULT)
    {
        if ($timezone == self::TIMEZONE_DEFAULT) {
            $timezone = $this->getTimezone();
        }

        /** @var DateTime $output */
        $output = clone $this->_start;
        $output->setTimezone(new DateTimeZone($timezone));

        return $output;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->_label = $label;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->_label;
    }

    /**
     * @param string $timezone
     * @return $this
     */
    public function setTimezone($timezone)
    {
        $this->_timezone = $timezone;

        return $this;
    }

    /**
     * @return string
     */
    public function getTimezone()
    {
        if (!$this->_timezone) {
            $this->_timezone = Mage::helper('SwiftOtter_Base')->getTimezone();
        }

        return $this->_timezone;
    }




}