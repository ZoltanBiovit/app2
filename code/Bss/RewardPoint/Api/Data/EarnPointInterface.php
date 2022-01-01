<?php
namespace Bss\RewardPoint\Api\Data;

interface EarnPointInterface
{
    /**
     * Constants defined for keys of array, makes typos less likely
     */
    const STATUS = 'status';

    const EARN_POINT = 'earn_point';


    /**
     * @return boolean
     */
    public function getStatus();

    /**
     * @param boolean $status
     * @return boolean
     */
    public function setStatus($status);

    /**
     * @return float
     */
    public function getEarnPoint();

    /**
     * @param int $point
     * @return float
     */
    public function setEarnPoint($point);
}
