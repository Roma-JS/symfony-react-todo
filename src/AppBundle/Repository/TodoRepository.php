<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Todo;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * TodoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TodoRepository extends EntityRepository
{
    /**
     * Persists todo to database if it doesn't exist in database.
     *
     * @param Todo $author
     * @return Todo
     */
    public function persistTodo(Todo $author)
    {
        if ($author->getId() !== null) {
            $author = $this->_em->merge($author);
        } else {
            $this->_em->persist($author);
        }

        $this->_em->flush();

        return $author;
    }

    /**
     * Deletes Todo.
     *
     * @param Todo $author
     */
    public function deleteTodo(Todo $author)
    {
        $this->_em->remove($author);
        $this->_em->flush();
    }
}
