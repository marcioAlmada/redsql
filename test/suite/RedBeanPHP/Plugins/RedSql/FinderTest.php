<?php

namespace RedBeanPHP\Plugins\RedSql;

use RedBean_Facade as R;

abstract class FinderTest extends \PHPUnit_Framework_TestCase
{
    protected $data = [
        ['name' => 'Alan Turing',         'birth' => 1912, 'death' => 1954, 'profession' => 'cryptanalyst', 'deleted' => true ],
        ['name' => 'Albert Einstein',     'birth' => 1879, 'death' => 1955, 'profession' => 'physicist',    'deleted' => false],
        ['name' => 'Machado de Assis',    'birth' => 1839, 'death' => 1908, 'profession' => 'writer',       'deleted' => false],
        ['name' => 'Sigmund Freud',       'birth' => 1856, 'death' => 1939, 'profession' => 'neurologist',  'deleted' => false],
        ['name' => 'Vincent van Gogh',    'birth' => 1853, 'death' => 1890, 'profession' => 'painter',      'deleted' => false],
        ['name' => 'William Shakespeare', 'birth' => 1564, 'death' => 1616, 'profession' => 'writer',       'deleted' => true ]
    ];

    public function setUp()
    {
        $this->createFixtures();
    }

    public function tearDown()
    {
        R::nuke();
    }

    /**
     * @test
     * @expectedException \RedBean_Exception_Security
     */
    public function failsWithInvalidBeanTypes()
    {
        R::redsql('InvalidBeanType');
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function failsWithUnsupportedStatement()
    {
        R::redsql('genius')->FOO->find();
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @dataProvider tokenDataProvider
     */
    public function failsApplyingFilterInWrongContext($token)
    {
        R::redsql('genius')->$token->find();
    }

    public function tokenDataProvider()
    {
        return [
            ['BETWEEN'],
            ['EQUALS'],
            ['GREATER'],
            ['GREATEROREQUALS'],
            ['LIKE'],
            ['ILIKE'],
            ['IN'],
            ['LESS'],
            ['LESSOREQUALS'],
            ['LIMIT'],
            ['OFFSET'],
            ['NOTEQUALS']
        ];
    }

    /**
     * @test
     */
    public function allowsRxSyntax()
    {
        $this->assertCount(1, R::redsql('genius')->profession('cryptanalyst')->birth(1912)->death(1954)->find());
    }

    /**
     * @test
     */
    public function allowNoConditions()
    {
        $this->assertCount(count($this->data), R::redsql('genius')->find());
    }

    /**
     * @test
     */
    public function supportsEqualsOperator()
    {
        $this->assertCount(1, R::redsql('genius')->name('=', 'Albert Einstein')->find());
        $this->assertCount(1, R::redsql('genius')->name('Albert Einstein')->find());

        $count = (count($this->data) - 4);
        $this->assertCount($count, R::redsql('genius')->deleted('=', true )->find());
        $this->assertCount($count, R::redsql('genius')->deleted(true)->find());

        $count = (count($this->data) - 2);
        $this->assertCount($count, R::redsql('genius')->deleted('=', false)->find());
        $this->assertCount($count, R::redsql('genius')->deleted(false)->find());
    }

    /**
     * @test
     */
    public function supportsNotEqualsOperators()
    {
        $count = (count($this->data) - 1);
        $this->assertCount($count, R::redsql('genius')->name('!=', 'Alan Turing')->find());
        $this->assertCount($count, R::redsql('genius')->name('<>', 'Alan Turing')->find());
        $this->assertCount($count, R::redsql('genius')->NOT->name('=', 'Alan Turing')->find());

        $count = (count($this->data) - 4);
        $this->assertCount($count, R::redsql('genius')->deleted('!=', false)->find());
        $this->assertCount($count, R::redsql('genius')->deleted('<>', false)->find());
        $this->assertCount($count, R::redsql('genius')->NOT->deleted( false)->find());

        $count = (count($this->data) - 2);
        $this->assertCount($count, R::redsql('genius')->deleted('!=', true )->find());
        $this->assertCount($count, R::redsql('genius')->deleted('<>', true )->find());
        $this->assertCount($count, R::redsql('genius')->NOT->deleted( true )->find());
    }

    /**
     * @test
     */
    public function supportsLikeOperator()
    {
        $this->assertCount(2, R::redsql('genius')->name('like', 'Al%')->find());
        $this->assertCount(1, R::redsql('genius')->name('LIKE', '%Einstein')->find());
        $this->assertCount(count($this->data), R::redsql('genius')->name('like', '%')->find());
        $this->assertCount(count($this->data), R::redsql('genius')->name('LIKE', '%%')->find());
    }

    /**
     * @test
     */
    public function supportsIlikeOperator()
    {
        $this->assertCount(2, R::redsql('genius')->name('ilike', 'AL%')->find());
        $this->assertCount(1, R::redsql('genius')->name('ILIKE', '%EINSTEIN')->find());
        $this->assertCount(count($this->data), R::redsql('genius')->name('ilike', '%')->find());
        $this->assertCount(count($this->data), R::redsql('genius')->name('ILIKE', '%%')->find());
    }

    /**
     * @test
     */
    public function supportsLessThanOperator()
    {
        $this->assertCount(1, R::redsql('genius')->birth('<', 1839)->find());
    }

    /**
     * @test
     */
    public function supportsLessOrEqualsToOperator()
    {
        $this->assertCount(4, R::redsql('genius')->death('<=', 1939)->find());
    }

    /**
     * @test
     */
    public function supportsGreaterThanOperator()
    {
        $this->assertCount(1, R::redsql('genius')->birth('>', 1879)->find());
    }

    /**
     * @test
     */
    public function supportsGreaterOrEqualsToOperator()
    {
        $this->assertCount(3, R::redsql('genius')->death('>=', 1939)->find());
    }

    /**
     * @test
     */
    public function supportsInOperator()
    {
        $this->assertCount(3,
            R::redsql('genius')
                ->profession('in', ['cryptanalyst', 'physicist', 'neurologist'])
                ->find());

        $this->assertCount(3,
            R::redsql('genius')
                ->NOT->profession('in', ['writer', 'painter'])
                ->find());

        $this->assertCount(3,
            R::redsql('genius')
                ->birth('>', 1853)
                ->profession('in', ['cryptanalyst', 'physicist', 'neurologist'])
                ->find());
    }

    /**
     * @test
     */
    public function emptyInValuesCreatesNoConditions()
    {
        $this->assertCount(count($this->data), R::redsql('genius')->birth('IN', [])->find());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function failsInWithNonArrays()
    {
        $this->assertCount(count($this->data), R::redsql('genius')->birth('IN', 1839, 1856)->find());
    }

    /**
     * @test
     */
    public function supportsBetweenOperator()
    {
        $this->assertCount(1, R::redsql('genius')->birth('between', [1900, 1950])->find());
        $this->assertCount(2, R::redsql('genius')->death('BETWEEN', [1900, 1950])->find());
        $this->assertCount(4, R::redsql('genius')->NOT->death('BETWEEN', [1900, 1950])->find());
    }

    /**
     * @test
     * @depends supportsBetweenOperator
     * @expectedException \InvalidArgumentException
     */
    public function failsBetweenWithWrongValuesCount()
    {
        $this->assertCount(1, R::redsql('genius')->birth('between', [1900])->find());
    }

    /**
     * @test
     */
    public function supportsAndOperator()
    {
        $this->assertCount(1, R::redsql('genius')->profession('writer')->AND->birth('<', 1600)->find());
    }

    /**
     * @test
     */
    public function supportsOrOperator()
    {
        $this->assertCount(3, R::redsql('genius')->profession('writer')->OR->profession('painter')->find());
    }

    /**
     * @test
     */
    public function supportsNotOperator()
    {
        $this->assertCount(5, R::redsql('genius')->NOT->name('Alan Turing')->find());

        $this->assertCount(4, R::redsql('genius')
            ->NOT
                ->OPEN
                    ->name('Alan Turing')->OR->name('Albert Einstein')
                ->CLOSE
            ->find());
    }

    /**
     * @test
     */
    public function allowGroupingConditions()
    {
        $people =
            R::redsql('genius')
                ->profession('painter')
                ->OR
                    ->OPEN
                        ->profession('writer')->AND->name('!=', 'William Shakespeare')
                    ->CLOSE
                ->find();
        $this->assertCount(2, $people);
    }

    /**
     * @test
     */
    public function allowFindWithLimitAndOffset()
    {
        $this->assertCount(0, R::redsql('genius')->find(0));
        $this->assertCount(0, R::redsql('genius')->find(0, 1));
        $this->assertCount(0, R::redsql('genius')->find(0, 0));

        $this->assertCount(1, R::redsql('genius')->find(1));
        $this->assertCount(1, R::redsql('genius')->find(1, 0));

        $this->assertCount(1, R::redsql('genius')->find(1));
        $this->assertCount(1, R::redsql('genius')->find(100, 5));
        $this->assertCount(6, R::redsql('genius')->find(100, 0));
    }

    /**
     * @test
     */
    public function findWithFields()
    {
        $result = R::redsql('genius', ['name', 'birth'])->find()[1];
        $this->assertEquals($this->data[0]['name'], $result->name);
        $this->assertEquals($this->data[0]['birth'], $result->birth);
        $this->assertSame(NULL, $result->death);
        $this->assertSame(NULL, $result->profession);
    }

    /**
     * @test
     */
    public function findFirst()
    {
        $this->assertEquals($this->data[0]['name'], R::redsql('genius', ['name'])->findFirst()->name);
        $this->assertEquals(1, R::redsql('genius', ['id'])->findFirst()->id);

        $null_object = R::redsql('genius', ['id'])->name('')->findFirst();
        $this->assertCount(1, $null_object);
        $this->assertEquals(0, $null_object->id);
    }

    /**
     * @test
     */
    public function findLast()
    {
        $this->assertEquals(end($this->data)['name'], R::redsql('genius', ['name'])->findLast()->name);
        $this->assertEquals(count($this->data), R::redsql('genius', ['name'])->findLast()->id);

        $null_object = R::redsql('genius', ['id'])->name('')->findLast();
        $this->assertCount(1, $null_object);
        $this->assertEquals(0, $null_object->id);
    }

    protected function createFixtures()
    {
        array_map(
            function ($data) {
                $project = R::dispense('genius');
                $project->import($data);
                R::store($project);
            },
            $this->data
        );
    }
}
