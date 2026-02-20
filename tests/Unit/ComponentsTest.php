<?php

namespace Thehouseofel\Kalion\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Thehouseofel\Kalion\Core\Domain\Support\Services\TailwindClassFilter;

class ComponentsTest extends TestCase
{
    private function assertClasses($default_classes, $custom_classes, $expected)
    {
        $filteredDefault = TailwindClassFilter::new()->filter($default_classes, $custom_classes);

        $array_filtered = explode(' ', $filteredDefault);
        $array_expected = explode(' ', $expected);

        try {
            $this->assertEqualsCanonicalizing($array_filtered,$array_expected, 'expected and actual are not canonically equals');
        } catch (\Throwable $th) {
            echo "Este test no lo pasa:\n";
            echo "filterTailwindClasses(\n";
            echo "$default_classes\n";
            echo "$custom_classes\n";
            echo ");\n";
            echo "Me devuelve \"$filteredDefault\"\n";
            echo "Y me debería devolver \"$expected\" (el orden de las clases da igual)\n\n";
            $this->fail($th->getMessage());
        }
    }

    /**
     * A basic test example.
     */
    public function test_macro_merge_tailwind_works(): void
    {
        $this->assertClasses(
            'text-md flex',
            'text-sm',
            'flex',
        );

        $this->assertClasses(
            'text-blue-500',
            'text-sm',
            'text-blue-500',
        );

        $this->assertClasses(
            'text-blue-500',
            'text-red-400',
            '',
        );

        $this->assertClasses(
            'text-blue-500 dark:text-blue-500',
            'text-red-400',
            'dark:text-blue-500',
        );

        $this->assertClasses(
            'text-md text-blue-500 bg-gray-50 dark:text-blue-500 hover:text-blue-500',
            'text-sm text-red-400',
            'bg-gray-50 dark:text-blue-500 hover:text-blue-500',
        );

        $this->assertClasses(
            'text-md text-blue-500 bg-gray-50 dark:text-blue-500 hover:text-blue-500',
            'text-sm text-red-400 dark:text-green-500',
            'bg-gray-50 hover:text-blue-500',
        );

        $this->assertClasses(
            'text-md text-blue-600 underline dark:text-blue-500 hover:text-blue-800 hover:underline',
            'text-xs text-red-600',
            'underline dark:text-blue-500 hover:text-blue-800 hover:underline',
        );

        $this->assertClasses(
            'text-md text-blue-600 underline dark:text-blue-500 hover:text-blue-800 hover:underline',
            'text-xs text-red-600 dark:text-green-400',
            'underline hover:text-blue-800 hover:underline',
        );

        $this->assertClasses(
            'mx-1 my-2 mb-3',
            'mx-s mx-sm flex',
            'my-2 mb-3',
        );

        $this->assertClasses(
            'flex',
            'block',
            '',
        );

        $this->assertClasses(
            'dark:flex',
            'block',
            'dark:flex',
        );

        $this->assertClasses(
            'inline dark:block static',
            'block fixed',
            'dark:block',
        );

        $this->assertClasses(
            'inline dark:block static',
            'block dark:table dark:fixed',
            'static',
        );

        $this->assertClasses(
            'bg-teal-500',
            'bg-white',
            '',
        );

        $this->assertClasses(
            'bg-teal-500 text-blue-500',
            'bg-white text-black',
            '',
        );

        $this->assertClasses(
            'text-center',
            'text-sm',
            'text-center',
        );

        $this->assertClasses(
            'text-sm',
            'text-center',
            'text-sm',
        );

        $this->assertClasses(
            'dark:text-center',
            'dark:text-sm',
            'dark:text-center',
        );

        $this->assertClasses(
            'dark:text-sm',
            'dark:text-center',
            'dark:text-sm',
        );
    }
}
