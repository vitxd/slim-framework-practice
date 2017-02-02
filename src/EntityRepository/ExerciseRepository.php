<?php
namespace BusuuTest\EntityRepository;

use Slim\Container;
use Throwable;

/*
 * TODO
 *
 * create an abstract class that implements repository interface
 * give that a constructor
 * then going forward, every time i add a new repository, it should have access to the container and values
 */

class ExerciseRepository implements RepositoryInterface

{
    private $container;

    /**
     * ExerciseRepository constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function findAll()
    {
        $query = "select * from exercises";
        return $this->runSelectQuery($query);
    }

    public function find($exerciseId)
    {
        $query = "select * from exercises where exerciseId = $exerciseId";
        return $this->runSelectQuery($query);
    }

    public function delete($exerciseId)
    {
        $query = "delete from exercises where exerciseId = $exerciseId";
        $queryResults = [];

        $stmt = $this->container['db']->prepare($query);

        try {
            $stmt->execute();
        } catch (Throwable $throwable) {
            $queryResults['Error'] = 'Error when querying the database';
            return $queryResults;
        }

        if ($stmt->affected_rows == 0) {
            return $queryResults;
        }

        $queryResults[] = "Exercise with exerciseId = $exerciseId deleted";

        return $queryResults;
    }

    public function save($requestParams)
    {
        $query = "INSERT INTO exercises (author, exerciseText) VALUES (?,?)";
        $queryResults = [];

        $stmt = $this->container['db']->prepare($query);

        try {
            $stmt->bind_param("ss", $requestParams['author'], $requestParams['exerciseText']);
            $stmt->execute();
        } catch (Throwable $throwable) {
            $queryResults['Error'] = 'Error when querying the database';
            return $queryResults;
        }

        if ($stmt->affected_rows == 0) {
            return $queryResults;
        }

        $queryResults[] = "Exercise saved";

        return $queryResults;
    }

    public function update($exerciseId, $requestParams)
    {
        $query = "UPDATE exercises SET author = ?, exerciseText = ? WHERE exercises.exerciseId = $exerciseId";
        $queryResults = [];

        $stmt = $this->container['db']->prepare($query);

        $stmt->bind_param("ss", $requestParams['author'], $requestParams['exerciseText']);

        try {
            $stmt->execute();
        } catch (Throwable $throwable) {
            $queryResults['Error'] = 'Error when querying the database';
            return $queryResults;
        }

        if ($stmt->affected_rows == 0) {
            return $queryResults;
        }

        $queryResults[] = "Exercise with exerciseId = $exerciseId updated";;

        return $queryResults;
    }

    private function runSelectQuery($query)
    {
        $queryResults = [];

        $stmt = $this->container['db']->prepare($query);

        try {
            $stmt->execute();
        } catch (Throwable $throwable) {
            $queryResults['Error'] = 'Error when querying the database';
            return $queryResults;
        }

        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $queryResults[] = $row;
        }

        return $queryResults;
    }
}