<?php
/**
 * @author Tim Sims <https://github.com/timsims>
 */
namespace Think;

use Think\Model;

/**
 * 每个测试方法前自动开启数据库事务，测试结束后自动回滚
 * trait DatabaseTransaction
 */
trait DatabaseTransaction
{
    protected $transactionModel;

    /**
     * @before
     */
    public function startTransaction()
    {
        $this->makeModel()->startTrans();
    }

    /**
     * @after
     */
    public function endTransaction()
    {
        $this->makeModel()->rollback();
    }

    protected function makeModel()
    {
        if (!$this->transactionModel) {
            $this->transactionModel = new Model;
        }
        return $this->transactionModel;
    }
}
