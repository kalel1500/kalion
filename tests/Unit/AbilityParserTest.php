<?php

use PHPUnit\Framework\TestCase;
use Thehouseofel\Kalion\Features\Auth\Domain\Support\AbilityParser;

class AbilityParserTest extends TestCase
{
    public function test_ability_parser()
    {
        $parser = new AbilityParser();

        $value1 = $parser->parse('admin_tags:1,2,3;4,5,6;aaaa;1|see_post_detail:7,8,9|filter_posts', [])->toArray();
        $value2 = $parser->parse("admin_tags|see_post_detail|filter_posts", [[[1,2,3],[4,5,6],'aaaa',1], [7,8,9]])->toArray();
//        dd($value1, $value2);
        $this->assertEquals($value1, $value2);
    }

}
