<?php

namespace RedBeanPHP\Plugins\RedSql;

use R;

abstract class FinderTest extends \PHPUnit_Framework_TestCase
{
    protected $data = [
        ['name' => 'Alan Turing',         'birth' => 1912, 'death' => 1954, 'profession' => 'cryptanalyst'],
        ['name' => 'Albert Einstein',     'birth' => 1879, 'death' => 1955, 'profession' => 'theoretical physicist'],
        ['name' => 'Machado de Assis',    'birth' => 1839, 'death' => 1908, 'profession' => 'writer'],
        ['name' => 'Sigmund Freud',       'birth' => 1856, 'death' => 1939, 'profession' => 'neurologist'],
        ['name' => 'Vincent van Gogh',    'birth' => 1853, 'death' => 1890, 'profession' => 'painter'],
        ['name' => 'William Shakespeare', 'birth' => 1564, 'death' => 1616, 'profession' => 'writer']
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
    }

    /**
     * @test
     */
    public function supportsNotEqualsOperators()
    {
        $count = count($this->data) -1;
        $this->assertCount($count, R::redsql('genius')->name('!=', 'Alan Turing')->find());
        $this->assertCount($count, R::redsql('genius')->name('<>', 'Alan Turing')->find());
        $this->assertCount($count, R::redsql('genius')->NOT->name('=', 'Alan Turing')->find());
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
        $this->assertCount(3, R::redsql('genius')->profession('in', ['cryptanalyst','theoretical physicist','neurologist'])->find());
        $this->assertCount(3, R::redsql('genius')->NOT->profession('in', ['writer', 'painter'])->find());
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
