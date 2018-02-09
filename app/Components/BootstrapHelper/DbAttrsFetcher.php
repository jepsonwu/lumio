<?php
namespace App\Components\BootstrapHelper;

use Prettus\Repository\Database\Eloquent\Model;
use Doctrine\DBAL\Schema\MySqlSchemaManager;

/**
 * Created by IntelliJ IDEA.
 * User: yidu
 * Date: 16/8/6
 * Time: 上午12:16
 */
class DbAttrsFetcher
{

    public static function fetchAttrs($class){

        if(!$class){
            throw new \RuntimeException('only supported eloquent model class');
        }

        /** @var Model $model */
        $model = app($class);

        $table = $model->getConnection()->getTablePrefix() . $model->getTable();
        /** @var MySqlSchemaManager $schema */
        $schema = $model->getConnection()->getDoctrineSchemaManager();
        $databasePlatform = $schema->getDatabasePlatform();
        $databasePlatform->registerDoctrineTypeMapping('enum', 'string');

        $laravel = app();

        $platformName = $databasePlatform->getName();
        $customTypes = $laravel['config']->get("ide-helper.custom_db_types.{$platformName}", array());
        foreach ($customTypes as $yourTypeName => $doctrineTypeName) {
            $databasePlatform->registerDoctrineTypeMapping($yourTypeName, $doctrineTypeName);
        }

        $database = null;
        if (strpos($table, '.')) {
            list($database, $table) = explode('.', $table);
        }

        $columns = $schema->listTableColumns($table, $database);

        $attrs = [];

        if ($columns) {

            foreach ($columns as $column) {
                $name = $column->getName();
                if (in_array($name, $model->getDates())) {
                    $type = 'date';
                } else {
                    $type = $column->getType()->getName();
                    switch ($type) {
                        case 'string':
                        case 'text':
                        case 'date':
                        case 'time':
                        case 'guid':
                        case 'datetimetz':
                        case 'datetime':
                            $type = 'string';
                            break;
                        case 'integer':
                        case 'bigint':
                        case 'smallint':
                            $type = 'integer';
                            break;
                        case 'decimal':
                        case 'float':
                            $type = 'float';
                            break;
                        case 'boolean':
                            $type = 'boolean';
                            break;
                        default:
                            $type = 'mixed';
                            break;
                    }
                }

                $comment = $column->getComment();
                $attrs[$name] = [
                    'name' => $name,
                    'type' => $type,
                    'comment' => $comment,
                ];
            }
            return $attrs;
        }
        return $attrs;
    }
}
