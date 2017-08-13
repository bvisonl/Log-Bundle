<?php

namespace Bvisonl\LogBundle\Services;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\DBAL\Connection;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Bvisonl\LogBundle\Entity\Log;

class Logger {

    /** @var  Connection $connection */
    private $connection;
    private $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        // Todo: Connection should be configurable and should be one dedicated to the log bundle
        $this->connection = $container->get('doctrine')->getConnection();
    }

    public function getUser()
    {

        if (null === $token = $this->container->get('security.token_storage')->getToken()) {
            // no authentication information is available
            return null;
        }

        if (!is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return null;
        }

        return $user->getUsername();
    }

    public function logNotice($message, $action = Log::ACTION_INFO, $entity = null) {
        $this->log($message, $action, $entity, Log::LEVEL_NOTICE);
    }

    public function logSuccess($message, $action = Log::ACTION_INFO, $entity = null) {
        $this->log($message, $action, $entity, Log::LEVEL_SUCCESS);
    }
    
    public function logWarning($message, $action = Log::ACTION_INFO, $entity = null) {
        $this->log($message, $action, $entity, Log::LEVEL_WARNING);
    }
    
    public function logDebug($message, $action = Log::ACTION_DEBUG, $entity = null) {
        $this->log($message, $action, $entity, Log::LEVEL_DEBUG);
    }    
    
    public function logError($message, $action = Log::ACTION_INFO, $entity = null) {
        $this->log($message, $action, $entity, Log::LEVEL_ERROR);
    }

    public function logApi($message, $level = Log::LEVEL_API) {
        $this->log($message, Log::ACTION_API, null, $level);
    }


    public function logException(\Exception $ex) {
        $this->log($ex->getMessage(), Log::ACTION_EXCEPTION, null, Log::LEVEL_ERROR, $ex);
    }

    private function log($message, $action = Log::ACTION_INFO, $entity = null, $level = Log::LEVEL_NOTICE, \Exception $ex = null) {

        if(null !== $entity) {

            if($this->container->hasParameter('bvisonl_log.exclusions')) {
                $excludes = $this->container->getParameter('bvisonl_log.exclusions');
            } else {
                $excludes = [Log::class];
            }

            if(in_array(get_class($entity), $excludes)){
                return;
            }

            $reader = new AnnotationReader();
            $excludeByAnnotation = $reader->getClassAnnotation(new \ReflectionClass($entity), 'Bvisonl\\LogBundle\\Annotations\\ExcludeDoctrineLogging');
            if($excludeByAnnotation) {
                return;
            }

            // Todo: This should be configurable
            $serializedEntity = $this->container->get('jms_serializer')->serialize($entity, 'json', SerializationContext::create()->setGroups(array("bvison_log"))->setSerializeNull(true));

        } else {
            $serializedEntity = "";
            $entity = "";
        }

        $log = new Log();
        $log->setAction($action);
        $log->setLevel($level);
        if(is_object($entity)) {
            $log->setEntity(get_class($entity));
        }
        $log->setMessage($message);
        $log->setDate();
        $log->setSerializedEntity($serializedEntity);
        $log->setUser($this->getUser());

        if(null !== $ex) {
            $log->setExceptionCode($ex->getCode());
            $log->setExceptionFile($ex->getFile());
            $log->setExceptionLine($ex->getLine());
        }

        $sqlParameters = array(
          "level" => $log->getLevel(),
          "action" => $log->getAction(),
          "entity" => $log->getEntity(),
          "message" => $log->getMessage(),
          "date" => $log->getDate()->format('Y-m-d h:i:s'),
          "ipaddress" => $log->getIpaddress(),
          "user" => $log->getUser(),
          "exceptionCode" => $log->getExceptionCode(),
          "exceptionFile" => $log->getExceptionFile(),
          "exceptionLine" => $log->getExceptionLine(),
          "serializedEntity" => $log->getSerializedEntity()
        );

        $fields = implode(', ', array_keys($sqlParameters));
        $values = array_values($sqlParameters);
        array_walk($values, function(&$val) {
            $val = '"'.addslashes($val).'"';
        });
        $values = implode(', ', $values);

        $sql = "INSERT INTO bvisonl_log({$fields}) VALUES({$values})";

        $stmt = $this->connection->prepare($sql);

        try{
            $stmt->execute();
        } catch(\Exception $ex) {
            error_log($ex->getMessage());
        }
    }
}