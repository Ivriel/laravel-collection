<?php

namespace Tests\Feature;

use App\Data\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CollectionTest extends TestCase
{
    public function testCreateCollection(): void
    {
        $collection = collect([1,2,3]);
        $this->assertEqualsCanonicalizing([1,2,3],$collection->all());
    }

    public function testForEach()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        foreach($collection as $key=>$value) {
            $this->assertEquals($key+1,$value);
        }
    }

    public function testCrud()
    {
        $collection = collect([]);
        $collection->push(1,2,3);
        $this->assertEqualsCanonicalizing([1,2,3],$collection->all());

        $result = $collection->pop();
        $this->assertEquals([1,2],$collection->all());     
    }

    public function testMap()
    {
        $collection  = collect([1,2,3]);
        $result = $collection->map(function($item) {
            return $item *2;
        });
        $this->assertEqualsCanonicalizing([2,4,6],$result->all());
    }

    public function testMapInto()
    {
        $collection = collect(["Ivriel"]);
        $result = $collection->mapInto(Person::class);
        $this->assertEquals([new Person("Ivriel")],$result->all());
    }

    public function testMapSpread()
    {
        $collection = collect([
            ["Ivriel","Gunawan"],
            ["Gunawan","Ivriel"]
        ]);

        $result = $collection->mapSpread(function($firstName,$lastName) {
            $fullName = $firstName ." ". $lastName;
            return new Person($fullName);
        });

        $this->assertEquals([
            new Person("Ivriel Gunawan"),
            new Person("Gunawan Ivriel"),
        ],$result->all());
    }

    public function testMapToGroups()
    {
        $collection = collect([
            [
                "name"=>"Ivriel",
                "department"=>"IT"
            ],
            [
                "name"=>"Gunawan",
                "department"=>"IT"
            ],
            [
                "name"=>"Budi",
                "department"=>"HR"
            ]
        ]);
        $result = $collection->mapToGroups(function($person){
            return [
                $person["department"] =>$person["name"]
            ];
        });

        $this->assertEquals([
            "IT"=> collect(["Ivriel","Gunawan"]),
            "HR"=> collect("Budi")
        ],$result->all());
    }

    public function testZip()
    {
        $collection1 = collect([1,2,3]);
        $collection2 = collect([4,5,6]);
        $collection3 = $collection1->zip($collection2);

        $this->assertEquals([
            collect([1,4]),
            collect([2,5]),
            collect([3,6]),
        ],$collection3->all());
    }

      public function testConcat()
    {
        $collection1 = collect([1,2,3]);
        $collection2 = collect([4,5,6]);
        $collection3 = $collection1->concat($collection2);

        $this->assertEquals([
            1,2,3,4,5,6
        ],$collection3->all());
    }

    public function testCombine()
    {
        $collection1 = collect(["name","country"]);
        $collection2 = collect(["Ivriel","Indonesia"]);
        $collection3 = $collection1->combine($collection2);

        $this->assertEqualsCanonicalizing([
            "name"=>"Ivriel",
            "country"=> "Indonesia"
        ],$collection3->all());
    }

    public function testCollapse()
    {
        $collection = collect([
            [1,2,3],
            [4,5,6],
            [7,8,9],
        ]);

        $result = $collection->collapse();
        $this->assertEqualsCanonicalizing([1,2,3,4,5,6,7,8,9,],$result->all());
    }

    public function testFlatMap()
    {
        $collection = collect([
            [
                "name"=>"Ivriel",
                "hobbies"=>["Coding","Gaming"]
            ],
             [
                "name"=>"Gunawan",
                "hobbies"=>["Reading","Writing"]
            ]
             ]);
            $result= $collection->flatMap(function($item){
                $hobbies = $item['hobbies'];
                return $hobbies;
            });
            $this->assertEqualsCanonicalizing(["Coding","Gaming","Reading","Writing"],$result->all());
    }

    public function testStringRepresentation()
    {
        $collection = collect(["Ivriel","Gunawan","Gunawan"]);
        $this->assertEquals("Ivriel-Gunawan-Gunawan",$collection->join("-"));
        $this->assertEquals("Ivriel-Gunawan_Gunawan",$collection->join("-","_"));
        $this->assertEquals("Ivriel, Gunawan and Gunawan",$collection->join(", "," and "));
    }

    public function testFilter()
    {
        $collection = collect([
            "Ivriel" => 100,
            'Budi' => 80,
            "Gunawan" => 90
        ]);
        $result = $collection->filter(function($value,$key){
            return $value >=90;
        });

        $this->assertEquals([
            "Ivriel"=>100,
            "Gunawan"=> 90,
        ],$result->all());
    }

    public function testFilterIndex()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9,10]);
        $result = $collection->filter(function($value,$key){
            return $value % 2 == 0;
        });

        $this->assertEqualsCanonicalizing([2,4,6,8,10],$result->all());
    }

    public function testPartition()
    {
        $collection = collect([
            "Ivriel"=>100,
            "Budi"=> 80,
            "Joko"=>90
        ]);

        [$result1,$result2] = $collection->partition(function($value,$key){
            return $value >=90;
        });

        $this->assertEquals([
            "Ivriel" =>100,
            "Joko"=> 90,
        ],$result1->all());
        $this->assertEquals([
            "Budi" =>80
        ],$result2->all());
    }

    public function testTesting()
    {
        $collection= collect(["Ivriel","Gunawan","Gunawan"]);
        $this->assertTrue($collection->contains("Ivriel"));
        $this->assertTrue($collection->contains(function($value,$key){
            return $value == "Gunawan";
        }));
    }

    public function testGrouping()
    {
        $collection = collect([
            [
                "name"=>"Ivriel",
                "department" =>"IT"
            ],
            [
                "name"=>"Gunawan",
                "department" =>"IT"
            ],
            [
                "name"=>"Budi",
                "department" =>"HR"
            ],
        ]);

        $result = $collection->groupBy("department");

        $this->assertEquals([
            "IT"=> collect([
                 [
                "name"=>"Ivriel",
                "department" =>"IT"
            ],
            [
                "name"=>"Gunawan",
                "department" =>"IT"
            ]
            ]),
            "HR"=> collect([
                 [
                "name"=>"Budi",
                "department" =>"HR"
            ],
            ])
            ],$result->all());

            $result = $collection->groupBy(function($value,$key) {
                return strtolower($value['department']); 
            });
             $this->assertEquals([
            "it"=> collect([
                 [
                "name"=>"Ivriel",
                "department" =>"IT"
            ],
            [
                "name"=>"Gunawan",
                "department" =>"IT"
            ]
            ]),
            "hr"=> collect([
                 [
                "name"=>"Budi",
                "department" =>"HR"
            ],
            ])
            ],$result->all());
    }

    public function testSlicing()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        $result = $collection->slice(3); // ambil data mulai index 3 sampai akhir

        $this->assertEqualsCanonicalizing([4,5,6,7,8,9],$result->all());

        $result = $collection->slice(3,2); // ambil data mulai index 3 dan ambil cuma 2 data aja
        $this->assertEqualsCanonicalizing([4,5],$result->all());
    }

    public function testTake()
    {
        $collection = collect([1,2,3,1,2,3,1,2,3]);
        $result = $collection->take(3);
        $this->assertEqualsCanonicalizing([1,2,3],$result->all());

        $result = $collection->takeUntil(function($value,$key){
            return $value == 3; // kondisi true tidak akan diambil
        });
        $this->assertEqualsCanonicalizing([1,2],$result->all());

          $result = $collection->takeWhile(function($value,$key){
            return $value < 3; // kondisi false tidak akan diambil
        });
        $this->assertEqualsCanonicalizing([1,2],$result->all());
    }

    public function testSkip()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        $result = $collection->skip(3);
        $this->assertEqualsCanonicalizing([4,5,6,7,8,9],$result->all());

        $result = $collection->skipUntil(function($value,$key){
            return $value == 3; // skip sampai valuenya sama dengan 3. jadi yang diambil dari 3 dan seterusnya
        });
        $this->assertEqualsCanonicalizing([3,4,5,6,7,8,9],$result->all());

        $result = $collection->skipWhile(function($value,$key){
            return $value < 3; // skip sampai valuenya false. jadi yang diambil dari 3 dan seterusnya
        });
        $this->assertEqualsCanonicalizing([3,4,5,6,7,8,9],$result->all());
    }

    public function testChunked()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9,10]);
        $result = $collection->chunk(3);

        $this->assertEqualsCanonicalizing([1,2,3],$result->all()[0]->all());
        $this->assertEqualsCanonicalizing([4,5,6],$result->all()[1]->all());
        $this->assertEqualsCanonicalizing([7,8,9],$result->all()[2]->all());
        $this->assertEqualsCanonicalizing([10],$result->all()[3]->all());
    }
    
    public function testFirst()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        $result = $collection->first();
        $this->assertEquals(1,$result);

        $result = $collection->first(function($value,$key){
            return $value > 5;
        });
        $this->assertEquals(6,$result);
    }

    public function testLast()
    {
        
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        $result = $collection->last();
        $this->assertEquals(9,$result);

        $result = $collection->last(function($value,$key){
            return $value < 5;
        });
        $this->assertEquals(4,$result);
    }

    public function testRandom()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        $result = $collection->random();

        $this->assertTrue(in_array($result,[1,2,3,4,5,6,7,8,9]));

        // $result = $collection->random(5);
        // $this->assertEqualsCanonicalizing([1,2,3,4,5],$result->all());
    }
}
