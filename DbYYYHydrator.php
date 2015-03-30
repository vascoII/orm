<?php
namespace YYY\Model;

use DateTime;
use Zend\Stdlib\Hydrator\HydratorInterface;
use YYY\Model\YYY;
/**
 * This class represents the database hydrator for yyy.
 */
class DbYYYHydrator implements HydratorInterface
{
    /**
     * Extract values from an object
     *
     * @param  object $object
     * @return array
     */
    public function extract($object)
    {
        if (!$object instanceof YYY)
        {
            throw new \Exception(YYY::NO_VALID_USER_OBJECT);
        }

        // Get properties.
        $data = [
            'attributY1'    => $object->attributY1,
            'attributY2'    => $object->attributY2,
        ];
        
        return $data;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  object $object
     * @return object
     */
    public function hydrate(array $data, $object)
    {
        if (!$object instanceof YYY)
        {
            throw new \Exception(YYY::NO_VALID_USER_OBJECT);
        }
        
        // Set properties.
        $object->attributY1   = $data['attributY1'];
        $object->attributY2   = $data['attributY2'];
        $object->createDate = new DateTime($data['create_date']);
        $object->updateDate = new DateTime($data['update_date']);
        return $object;
    }
}