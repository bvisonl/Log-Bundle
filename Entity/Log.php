<?php
namespace Bvisonl\LogBundle\Entity;

use Bvisonl\LogBundle\Annotations\ExcludeDoctrineLogging;
use Doctrine\ORM\Mapping as ORM;

/**
 * Log
 * @ExcludeDoctrineLogging()
 * @ORM\Table(name="bvisonl_log")
 * @ORM\Entity(repositoryClass="Bvisonl\LogBundle\Repositories\LogRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Log
{
    const LEVEL_NOTICE = "NOTICE";
    const LEVEL_ERROR = "ERROR";
    const LEVEL_WARNING = "WARNING";
    const LEVEL_SUCCESS = "SUCCESS";
    const LEVEL_DEBUG = "DEBUG";
    const LEVEL_API = "API";

    const ACTION_CREATE = "CREATE";
    const ACTION_UPDATE = "UPDATE";
    const ACTION_DELETE = "DELETE";
    const ACTION_EXCEPTION = "EXCEPTION";
    const ACTION_INFO = "INFO";
    const ACTION_DEBUG = "DEBUG";
    const ACTION_API = "API";

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="level", type="string", length=255)
     */
    private $level;
    /**
     * @var string
     *
     * @ORM\Column(name="action", type="string", length=255)
     */
    private $action;
    /**
     * @var string
     *
     * @ORM\Column(name="entity", type="text", nullable=true)
     */
    private $entity;
    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     */
    private $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
    /**
     * @var string
     *
     * @ORM\Column(name="ipaddress", type="string", length=255, nullable=true)
     */
    private $ipaddress;

    /**
     * @var string
     *
     * @ORM\Column(name="user", type="string", length=255, nullable=true)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="exceptionCode", type="text", nullable = true)
     */
    private $exceptionCode;

    /**
     * @var string
     *
     * @ORM\Column(name="exceptionFile", type="text", nullable = true)
     */
    private $exceptionFile;

    /**
     * @var string
     *
     * @ORM\Column(name="exceptionLine", type="text", nullable = true)
     */
    private $exceptionLine;

    /**
     * @var json
     *
     * @ORM\Column(name="serializedEntity", type="text", nullable=true)
     */
    private $serializedEntity;

    public function __construct() {
        $this->level = self::LEVEL_NOTICE;
    }

    public function getLabelColor() {
        switch($this->level) {
            case self::LEVEL_WARNING:
                return "warning";
            case self::LEVEL_ERROR:
                return "danger";
            case self::LEVEL_NOTICE:
                return "info";
            case self::LEVEL_SUCCESS:
                return "success";
            case self::LEVEL_DEBUG:
                return "purple";
            default:
                return "default";
        }
    }
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * Set action
     *
     * @param string $action
     * @return Log
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }
    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }
    /**
     * Set entity
     *
     * @param string $entity
     * @return Log
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
        return $this;
    }
    /**
     * Get entity
     *
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }
    /**
     * Set date
     *
     * @ORM\PrePersist()
     * @return Log
     */
    public function setDate()
    {
        $this->date = new \DateTime();
        return $this;
    }
    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
    /**
     * Set ipaddress
     *
     * @param string $ipaddress
     * @return Log
     */
    public function setIpaddress($ipaddress)
    {
        $this->ipaddress = $ipaddress;
        return $this;
    }
    /**
     * Get ipaddress
     *
     * @return string
     */
    public function getIpaddress()
    {
        return $this->ipaddress;
    }
    /**
     * Set serializedEntity
     *
     * @param string $serializedEntity
     * @return Log
     */
    public function setSerializedEntity($serializedEntity)
    {
        $this->serializedEntity = $serializedEntity;
        return $this;
    }
    /**
     * Get serializedEntity
     *
     * @return string
     *
     */
    public function getSerializedEntity()
    {
        return $this->serializedEntity;
    }

    /**
     * @return mixed
     */
    public function getDecodedEntity() {
        return json_decode($this->serializedEntity, true);
    }


    /**
     * Set level
     *
     * @param string $level
     * @return Log
     */
    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }
    /**
     * Get level
     *
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }
    /**
     * Set exceptionCode
     *
     * @param string $exceptionCode
     * @return Log
     */
    public function setExceptionCode($exceptionCode)
    {
        $this->exceptionCode = $exceptionCode;
        return $this;
    }
    /**
     * Get exceptionCode
     *
     * @return string
     */
    public function getExceptionCode()
    {
        return $this->exceptionCode;
    }
    /**
     * Set exceptionFile
     *
     * @param string $exceptionFile
     * @return Log
     */
    public function setExceptionFile($exceptionFile)
    {
        $this->exceptionFile = $exceptionFile;
        return $this;
    }
    /**
     * Get exceptionFile
     *
     * @return string
     */
    public function getExceptionFile()
    {
        return $this->exceptionFile;
    }
    /**
     * Set exceptionLine
     *
     * @param string $exceptionLine
     * @return Log
     */
    public function setExceptionLine($exceptionLine)
    {
        $this->exceptionLine = $exceptionLine;
        return $this;
    }
    /**
     * Get exceptionLine
     *
     * @return string
     */
    public function getExceptionLine()
    {
        return $this->exceptionLine;
    }
    /**
     * Set user
     *
     * @param string $user
     * @return Log
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }
    /**
     * Get user
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return Log
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }
}
