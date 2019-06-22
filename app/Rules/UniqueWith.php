<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UniqueWith implements Rule
{
    public static function makeOtherColumn($name, $value)
    {
        return (object)['name' => $name, 'value' => $value];
    }

    private $table;
    private $mainColumn;
    private $otherColumns;

    /**
     * Create a new rule instance.
     * Use the static function "makeOtherColumn" to create each of the "otherColumns"
     * 
     * @return void
     */
    public function __construct($table, $mainColumn, ...$otherColumns)
    {
        $this->table = $table;
        $this->mainColumn = $mainColumn;
        $this->otherColumns = collect($otherColumns);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $queryBuilder = DB::table($this->table);

        $queryBuilder->where($this->mainColumn, $value);

        $this->otherColumns->each(function ($otherColumn) use ($queryBuilder) {
            $queryBuilder->where($otherColumn->name, $otherColumn->value);
        });

        return $queryBuilder->count() === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $mustBeUniqueColumns = $this->otherColumns
            ->map(function ($otherColumn) {
                return $otherColumn->name;
            })
            ->prepend($this->mainColumn)
            ->join(', ');

        return 'The following column combination must be unique: ' . $mustBeUniqueColumns;
    }
}
