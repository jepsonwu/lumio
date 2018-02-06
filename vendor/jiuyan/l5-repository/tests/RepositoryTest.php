<?php

namespace Prettus;

use Prettus\Repository\Eloquent\BaseRepository;
use Illuminate\Support\Collection;

class RepositoryTest
{
    /**
     * @var BaseRepository
     */
    protected $userGoldRepository;

    /**
     * @var BaseRepository
     */
    protected $userGoldBackRepository;

    public function demoTest()
    {
        /**
         * model
         */
        //$log = new UserGoldLog();

        //$log->create(["user_id" => 2000, "operation" => 3]);

        //$log->where("user_id", 2000)->update(["operation" => 7]);

        //var_dump($log->where("user_id", 2000)->delete());

        //var_dump($log::destroy([2000, 2001]));

        //print_r($log->find(2000)->getAttributes());

        //print_r($log->findMany([2000])->first()->getAttributes());

        //print_r($log->where("operation", 3)->where("user_id", 2000)->first()->getAttributes());

        //print_r($log->where("user_id", 2000)->whereIn("operation", ["3"])->first()->getAttributes());

        /**
         * repository
         */
        //$this->create(["user_id" => 2002, "operation" => 9]);

        //$this->create(["user_id" => 2003, "operation" => 8]);

        //$this->update(["operation" => 10], 2002);

        //$this->delete(2003);

        //print_r($this->find(2003)->getAttributes());

        //print_r($this->findByField("user_id",2002)->first()->getAttributes());

        //print_r($this->findWhere(["user_id" => 2002])->first()->getAttributes());

        //print_r($this->findWhereIn("operation", [3])); //不支持
    }

    public function transactionTest()
    {
        $this->userGoldBackRepository->beginTransaction(new Collection([
            $this->userGoldBackRepository,
            $this->userGoldRepository
        ]));
        try {
            $this->userGoldRepository->addLog();

            print_r($this->userGoldRepository->find(372828457)->operation);

            $this->userGoldBackRepository->addLog();

            throw new \Exception("aa");

            $this->userGoldBackRepository->commit();
        } catch (\Exception $exception) {
            $this->userGoldBackRepository->rollBack();
        }

        print_r($this->userGoldRepository->find(372828457)->operation);
    }
}