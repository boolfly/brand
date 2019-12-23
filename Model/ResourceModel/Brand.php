<?php declare(strict_types=1);

namespace Boolfly\Brand\Model\ResourceModel;

use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Zend_Db_Select;

class Brand extends AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('bf_brand_entity', 'entity_id');
    }

    /**
     * @param AbstractModel $object
     * @return AbstractDb
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if (!$this->isValidUrlKey($object)) {
            throw new LocalizedException(
                __('The brand URL key contains capital letters or disallowed symbols.')
            );
        }
        if ($this->isNumericUrlKey($object)) {
            throw new LocalizedException(
                __('The brand URL key cannot be made of only numbers.')
            );
        }
        return parent::_beforeSave($object);
    }

    /**
     * @param AbstractModel $object
     * @return false|int
     */
    protected function isValidUrlKey(AbstractModel $object)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('url_key'));
    }
    /**
     *  Check whether post url key is numeric
     *
     * @param AbstractModel $object
     * @return bool
     */
    protected function isNumericUrlKey(AbstractModel $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('url_key'));
    }

    /**
     * @param $url_key
     * @return string
     * @throws LocalizedException
     */
    public function checkUrlKey($url_key)
    {
        $select = $this->getLoadByUrlKeySelect($url_key, 1);
        $select->reset(Zend_Db_Select::COLUMNS)->columns('br.entity_id')->limit(1);
        return $this->getConnection()->fetchOne($select);
    }

    /**
     * @param $url_key
     * @param null $isActive
     * @return Select
     * @throws LocalizedException
     */
    protected function getLoadByUrlKeySelect($url_key, $isActive = null)
    {
        $select = $this->getConnection()->select()->from(
            ['br' => $this->getMainTable()]
        )->where(
            'br.url_key = ?',
            $url_key
        );
        if (!is_null($isActive)) {
            $select->where('br.visibility = ?', $isActive);
        }
        return $select;
    }
}
