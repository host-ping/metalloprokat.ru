<?php

namespace Metal\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="sphinx_search_log",
 *   indexes={@ORM\Index(name="IDX_query_hash", columns={"query_hash"})}
 *)
 */
class SphinxSearchLog
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime", name="created_at"), nullable=false
     */
    protected $createdAt;

    /**
     * @ORM\Column(length=5000, name="raw_query", nullable=true)
     */
    protected $rawQuery;

    /**
     * @ORM\Column(type="integer", name="conn")
     */
    protected $conn;

    /**
     * @ORM\Column(type="decimal", scale=3, name="time_real")
     */
    protected $timeReal;

    /**
     * @ORM\Column(type="decimal", scale=3, name="time_wall")
     */
    protected $timeWall;

    /**
     * @ORM\Column(type="integer", name="founds")
     */
    protected $founds;

    /**
     * @ORM\Column(type="binary", length=20, name="query_hash")
     */
    protected $queryHash;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getRawQuery()
    {
        return $this->rawQuery;
    }

    /**
     * @param mixed $rawQuery
     */
    public function setRawQuery($rawQuery)
    {
        $this->rawQuery = $rawQuery;
        $this->queryHash = hex2bin(sha1($rawQuery));
    }

    /**
     * @return mixed
     */
    public function getConn()
    {
        return $this->conn;
    }

    /**
     * @param mixed $conn
     */
    public function setConn($conn)
    {
        $this->conn = $conn;
    }

    /**
     * @return mixed
     */
    public function getTimeReal()
    {
        return $this->timeReal;
    }

    /**
     * @param mixed $timeReal
     */
    public function setTimeReal($timeReal)
    {
        $this->timeReal = $timeReal;
    }

    /**
     * @return mixed
     */
    public function getTimeWall()
    {
        return $this->timeWall;
    }

    /**
     * @param mixed $timeWall
     */
    public function setTimeWall($timeWall)
    {
        $this->timeWall = $timeWall;
    }

    /**
     * @return mixed
     */
    public function getFounds()
    {
        return $this->founds;
    }

    /**
     * @param mixed $founds
     */
    public function setFounds($founds)
    {
        $this->founds = $founds;

    }

    public function getQueryHash()
    {
        return $this->queryHash;
    }

    public function setQueryHash($queryHash)
    {
        $this->queryHash = $queryHash;
    }
}
