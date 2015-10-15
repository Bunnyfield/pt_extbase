<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Felix Oertel <f@oer.tel>
*  All rights reserved
*
*  This class is a backport of the corresponding class of FLOW3.
*  All credits go to the v5 team.
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * A dispatcher which dispatches signals by calling its registered slot methods
 * and passing them the method arguments which were originally passed to the
 * signal method.
 *
 * @todo The signal slot dispatcher was introduces in extbase 1.4.0. This is a 1:1 copy of the class to use it in pre 1.4.0
 * @todo Remove this class and rename all usages as soon as we are on 1.4.0 everywhere
 *
 *
 * @api
 */
class Tx_PtExtbase_SignalSlot_Dispatcher implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Information about all slots connected a certain signal.
     * Indexed by [$signalClassName][$signalMethodName] and then numeric with an
     * array of information about the slot
     * @var array
     */
    protected $slots = array();

    /**
     * Injects the object manager
     *
     * @param \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
     * @return void
     */
    public function injectObjectManager(\TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Connects a signal with a slot.
     * One slot can be connected with multiple signals by calling this method multiple times.
     *
     * @param string $signalClassName Name of the class containing the signal
     * @param string $signalName Name of the signal
     * @param mixed $slotClassNameOrObject Name of the class containing the slot or the instantiated class or a Closure object
     * @param string $slotMethodName Name of the method to be used as a slot. If $slotClassNameOrObject is a Closure object, this parameter is ignored
     * @param boolean $omitSignalInformation If set to TRUE, the first argument passed to the slot will be the first argument of the signal instead of some information about the signal.
     * @return void
     * @api
     */
    public function connect($signalClassName, $signalName, $slotClassNameOrObject, $slotMethodName = '', $omitSignalInformation = false)
    {
        $class = null;
        $object = null;

        if (is_object($slotClassNameOrObject)) {
            $object = $slotClassNameOrObject;
            $method = ($slotClassNameOrObject instanceof Closure) ? '__invoke' : $slotMethodName;
        } else {
            if ($slotMethodName === '') {
                throw new InvalidArgumentException('The slot method name must not be empty (except for closures).', 1229531659);
            }
            $class = $slotClassNameOrObject;
            $method = $slotMethodName;
        }

        $slot = array(
            'class' => $class,
            'method' => $method,
            'object' => $object,
            'omitSignalInformation' => ($omitSignalInformation === true)
        );

        if (!is_array($this->slots[$signalClassName][$signalName]) || !in_array($slot, $this->slots[$signalClassName][$signalName])) {
            $this->slots[$signalClassName][$signalName][] = $slot;
        }
    }

    /**
     * Dispatches a signal by calling the registered Slot methods
     *
     * @param string $signalClassName Name of the class containing the signal
     * @param string $signalName Name of the signal
     * @param array $signalArguments arguments passed to the signal method
     * @return void
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException if the slot is not valid
     * @api
     */
    public function dispatch($signalClassName, $signalName, array $signalArguments = array())
    {
        if (!isset($this->slots[$signalClassName][$signalName])) {
            return;
        }
        foreach ($this->slots[$signalClassName][$signalName] as $slotInformation) {
            if (isset($slotInformation['object'])) {
                $object = $slotInformation['object'];
            } else {
                if (!isset($this->objectManager)) {
                    throw new \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException(sprintf('Cannot dispatch %s::%s to class %s. The object manager is not yet available in the Signal Slot Dispatcher and therefore it cannot dispatch classes.', $signalClassName, $signalName, $slotInformation['class']), 1298113624);
                }
                if (!$this->objectManager->isRegistered($slotInformation['class'])) {
                    throw new \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException('The given class "' . $slotInformation['class'] . '" is not a registered object.', 1245673367);
                }
                $object = $this->objectManager->get($slotInformation['class']);
            }
            $slotArguments = $signalArguments;
            if ($slotInformation['omitSignalInformation'] !== true) {
                $slotArguments[] = $signalClassName . '::' . $signalName;
            }
            if (!method_exists($object, $slotInformation['method'])) {
                throw new \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException('The slot method ' . get_class($object) . '->' . $slotInformation['method'] . '() does not exist.', 1245673368);
            }
            call_user_func_array(array($object, $slotInformation['method']), $slotArguments);
        }
    }

    /**
     * Returns all slots which are connected with the given signal
     *
     * @param string $signalClassName Name of the class containing the signal
     * @param string $signalName Name of the signal
     * @return array An array of arrays with slot information
     * @api
     */
    public function getSlots($signalClassName, $signalName)
    {
        return (isset($this->slots[$signalClassName][$signalName])) ? $this->slots[$signalClassName][$signalName] : array();
    }
}
