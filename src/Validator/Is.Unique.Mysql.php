<?php
/**
 * @author          Remco van der Velde
 * @since           2020-09-18
 * @copyright       Remco van der Velde
 * @license         MIT
 * @version         1.0
 * @changeLog
 *     -            all
 */

use Raxon\App;
use Raxon\Module\Database;

use Raxon\Exception\ObjectException;
use Raxon\Exception\FileWriteException;

/**
 * @throws ObjectException
 * @throws FileWriteException
 * @throws \Doctrine\ORM\Exception\ORMException
 * @throws \Doctrine\ORM\ORMException
 * @throws Exception
 */
function validate_is_unique_mysql(App $object, $string='', $field='', $argument='', $function=false): bool
{
    $table = false;
    $field = false;
    if(property_exists($argument, 'table')){
        $table = $argument->table;
    }
    if(property_exists($argument, 'field')){
        $field = $argument->field;
    }
    if(
        $table &&
        $field
    ){
        $entityManager = Database::entityManager($object, ['name' => Database::SYSTEM]);
        $uuid = $object->request('uuid');
        $id = $object->request('id');
        if($uuid){
            $qb = $entityManager->createQueryBuilder();
            $record = $qb->select(['entity'])
                ->from($table, 'entity')
                ->where('entity.uuid != :uuid')
                ->andWhere('entity.' . $field . ' = :'  . $field)
                ->setParameters([
                    'uuid' => $uuid,
                    $field => $string
                ])
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        }
        elseif($id){
            $qb = $entityManager->createQueryBuilder();
            $record = $qb->select(['entity'])
                ->from($table, 'entity')
                ->where('entity.id != :id')
                ->andWhere('entity.' . $field . ' = :'  . $field)
                ->setParameters([
                    'id' => $id,
                    $field => $string
                ])
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        } else {
            $repository = $entityManager->getRepository($table);
            $criteria = [];
            $criteria[$field] = $string;
            $record = $repository->findOneBy($criteria);
        }
        if($record === null){
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}
