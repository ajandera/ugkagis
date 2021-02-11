<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\Model\Settings\Storage\Doctrine;

use App\Model\Settings\Entity\Settings;
use App\Model\Settings\Storage\ISettingsDao;
use Doctrine\ORM\Mapping;
use Kdyby\Doctrine\EntityDao AS DoctrineEntityDao;

/**
 * Class SettingsDao
 * @package App\Model\Settings\Storage\Doctrine
 */
class SettingsDao implements ISettingsDao
{
    /** @var DoctrineEntityDao */
    private $settingsDao;

    public function __construct(DoctrineEntityDao $settingsDao)
    {
        $this->settingsDao = $settingsDao;
    }

    /**
     * Persist settings to storage.
     * @param Settings $settings
     */
    public function save(Settings $settings)
    {
        $this->settingsDao->getEntityManager()->persist($settings);
        $this->settingsDao->getEntityManager()->flush();
    }

    /**
     * Delete Settings from storage.
     * @param Settings $settings
     */
    public function delete(Settings $settings)
    {
        $this->settingsDao->getEntityManager()->remove($settings);
        $this->settingsDao->getEntityManager()->flush();
    }

    /**
     * Find all settings.
     * @return Settings[]
     */
    public function findAll()
    {
        return $this->settingsDao->findAll();
    }

    /**
     * Find settings by id
     * @param int $settingsId
     * @return Settings|mixed
     */
    public function findById($settingsId)
    {
        return $this->settingsDao->find($settingsId);
    }

    /**
     * Find settings by conditions
     * @param array $conditions
     * @return Settings[]
     */
    public function findBy($conditions = [])
    {
        return $this->settingsDao->findBy($conditions);
    }

    /**
     * Find one settings by conditions.
     * @param array $conditions
     * @return Settings
     */
    public function findOneBy($conditions = [])
    {
        return $this->settingsDao->findOneBy($conditions);
    }

    /**
     * Returns count of all settings.
     * @param array $conditions
     * @return int
     */
    public function countOfAll($conditions = [])
    {
        $qb = $this->settingsDao->createQueryBuilder('t');
        $qb->select('count(t.id)');
        $qb->whereCriteria($conditions);

        return $qb->getQuery()->getSingleScalarResult();
    }
}
