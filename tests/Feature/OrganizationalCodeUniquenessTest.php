<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class OrganizationalCodeUniquenessTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('organizationalTables')]
    public function test_organizational_codes_have_database_unique_constraints(string $table): void
    {
        $uniqueIndexes = collect(Schema::getIndexes($table))
            ->filter(fn (array $index): bool => $index['unique'])
            ->pluck('columns');

        $this->assertTrue(
            $uniqueIndexes->contains(['code']),
            "The [{$table}.code] column does not have a unique index.",
        );
    }

    /**
     * @return array<string, array{string}>
     */
    public static function organizationalTables(): array
    {
        return [
            'badan usaha' => ['business_entities'],
            'division' => ['divisions'],
            'region' => ['regions'],
            'cluster' => ['clusters'],
        ];
    }
}
