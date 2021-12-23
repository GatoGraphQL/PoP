<?php

declare(strict_types=1);

namespace PoPBackbone\GraphQLParser\Parser;

use PoPBackbone\GraphQLParser\Execution\Request;
use PoPBackbone\GraphQLParser\Parser\Ast\Fragment;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{

    public function testMethods()
    {
        $fragment1     = new Fragment('fragmentName1', 'test', [], [], new Location(1, 1));
        $fragment2     = new Fragment('fragmentName2', 'test', [], [], new Location(1, 1));
        $queriesData   = ['query1', 'query2'];
        $mutationsData = ['mutation1', 'mutation2'];
        $fragmentsData = [$fragment1];
        $variableValues     = [
            'page' => 2
        ];

        $request = new Request();
        $request->process([
            'queries'   => $queriesData,
            'mutations' => $mutationsData,
            'fragments' => $fragmentsData,
        ]);
        $request->setVariableValues($variableValues);

        $this->assertEquals($queriesData, $request->getQueryOperations());
        $this->assertEquals($mutationsData, $request->getMutationOperations());
        $this->assertEquals($fragmentsData, $request->getFragments());
        $this->assertEquals($variableValues, $request->getVariableValues());

        $this->assertTrue($request->hasFragments());
        $this->assertTrue($request->hasMutationOperations());
        $this->assertTrue($request->hasQueryOperations());

        $this->assertTrue($request->hasVariable('page'));
        $this->assertEquals(2, $request->getVariableValue('page'));

        $request->addFragment($fragment2);
        $this->assertEquals($fragment2, $request->getFragment('fragmentName2'));
        $this->assertNull($request->getFragment('unknown fragment'));
    }
}
