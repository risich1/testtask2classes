<?php

namespace App;

use Exception;

/**
 * @property int $id
 * @property int $age
 * @property int $gender
 * @property string $city
 * @property string $last_name
 * @property string $first_name
 * @property string $date_birth
 */
class User extends Model {

    protected array $fields = [
        'id' => 'int',
        'first_name' => 'only_chars',
        'last_name' => 'only_chars',
        'date_birth' => 'sql_date',
        'gender' => 'int{0,1}',
        'city' => 'string'
    ];

    protected string $table = 'users';

    public static function convertDateToAge(string $date): int {
        $date = date('Ymd', strtotime($date));
        $diff = date('Ymd') - $date;
        return (int) substr($diff, 0, -4);
    }

    public static function convertGender(int $sex): string {
        $genders =  ['male', 'female'];
        return $genders[$sex];
    }

    /**
     * @throws Exception
     */
    public function transform(): object {
        $data = [
            ...$this->data,
            'age' => static::convertDateToAge($this->date_birth),
            'gender' => static::convertGender($this->gender)
        ];
        return (object) $data;
    }

}
