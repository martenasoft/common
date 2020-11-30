<?php

namespace MartenaSoft\Common\Service\ConfigService;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use MartenaSoft\Common\Entity\CommonEntityConfigInterface;
use MartenaSoft\Content\Entity\ConfigInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CommonConfigService implements CommonConfigServiceInterface
{
    public const ENTITY_CONFIG_NAME = 'entity_config';

    private ParameterBagInterface $parameterBag;
    private EntityManagerInterface $entityManager;
    private bool $isGenerateValueFromEntityIfEmpty = true;

    public function __construct(EntityManagerInterface $entityManager, ParameterBagInterface $parameterBag)
    {
        $this->entityManager = $entityManager;
        $this->parameterBag = $parameterBag;
    }

    public function get(string $name): array
    {
        $config = $this->parameterBag->get($name);
        if (!empty($config[self::ENTITY_CONFIG_NAME]) &&
            class_exists($config[self::ENTITY_CONFIG_NAME]) &&
            ($entity = new $config[self::ENTITY_CONFIG_NAME]()) instanceof CommonEntityConfigInterface
        ) {
            $configDbQueryBuilder = $this->getConfigQueryBuilder($config[self::ENTITY_CONFIG_NAME]);
            $result = $configDbQueryBuilder->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);

            if (!empty($result)) {
                $config = array_merge($config, $result);
            } else {
                $config = array_merge($config, $this->defaultValueFormEntity($entity));
            }
        }
        return $config;
    }

    public function isEntity2DefaultValue($isGenerateValueFromEntityIfEmpty): self
    {
        $this->isGenerateValueFromEntityIfEmpty = $isGenerateValueFromEntityIfEmpty;
    }

    public function array2ConfigEntity(array $config, ConfigInterface $entity): ?ConfigInterface
    {
        if (empty($config)) {
            return null;
        }

        foreach ($config as $key => $value) {
            $setter = 'set'.ucfirst($key);

            if (method_exists($entity, $setter)) {
                try {
                    $entity->$setter($value);
                } catch (\Throwable $exception) {
                    throw $exception;
                }
            }
        }
        return $entity;
    }

    private function defaultValueFormEntity(CommonEntityConfigInterface $commonEntityConfig): array
    {
        $config = [];
        if (!$this->isGenerateValueFromEntityIfEmpty) {
            return $config;
        }

        $reflectionClass = new \ReflectionClass($commonEntityConfig);
        foreach ($reflectionClass->getProperties() as $property) {
            $getter = 'get' . ucfirst($property->getName());
            if (method_exists($commonEntityConfig, $getter)) {
                try {
                    $config[$property->getName()] = $commonEntityConfig->$getter();
                } catch (\Throwable $exception) {
                    $config[$property->getName()] = '';
                }
            }
        }
        return $config;
    }

    private function getConfigQueryBuilder(
        string $className,
        string $name = CommonEntityConfigInterface::DEFAULT_NAME
    ): QueryBuilder {
        $repository = $this->entityManager->getRepository($className);
        $queryBuilder = null;

        if (method_exists($repository, 'getConfigQueryBuilder') &&
            ($queryBuilder = $repository->getConfigQueryBuilder($name)) instanceof QueryBuilder
        ) {
            $queryBuilder = $repository->getConfigQueryBuilder($name);
        }

        if (empty($queryBuilder)) {
            $queryBuilder = $repository
                ->createQueryBuilder('_config_alias')
                ->andWhere('_config_alias.name=:name')
                ->setParameter('name', $name);
        }
        return $queryBuilder;
    }
}
