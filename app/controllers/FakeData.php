<?php


namespace app;

use Aura\SqlQuery\QueryFactory;
use Faker\Factory;
use PDO;

class FakeData
{

    private $queryFactory;
    private $faker;
    protected $pdo;

    public function __construct(PDO $pdo, QueryFactory $queryFactory)
    {
        $this->pdo = $pdo;
        $this->faker = Factory::create();
        $this->queryFactory = $queryFactory;
    }

    public function fakeData()
    {
        $insert = $this->queryFactory->newInsert();

        $insert->into('users');

        for ($i = 0; $i < 30; $i++) {
            $insert->cols([
                'email' => $this->faker->email,
                'password' => $this->faker->password,
                'username' => $this->faker->name,
                'registered' => $this->faker->unixTime,
                'photo' => $this->faker->imageUrl(800, 400, 'people', true, 'Faker'),
                'vk' => $this->faker->userName,
                'telegram' => $this->faker->userName,
                'instagram' => $this->faker->userName,
                'mobile' => $this->faker->phoneNumber,
                'adres' => $this->faker->address,
                'workplace' => $this->faker->company
            ]);
            $insert->addRow();
        }
        $sth = $this->pdo->prepare($insert->getStatement());
        $sth->execute($insert->getBindValues());
        d($sth);
    }
}

