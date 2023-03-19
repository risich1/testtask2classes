<?php

namespace App;

class SourceFabric {

    public static function getSource(string $source): ISource {
        return match ($source) {
            'db' => new DB('mariadb', 'testclass', 'root', 'root')
        };
    }

}
