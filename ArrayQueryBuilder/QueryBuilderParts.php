<?php

namespace App\Libraries\Larocket\ArrayQueryBuilder;

use Illuminate\Support\Facades\DB;
use App\Libraries\Larocket\CoreHandlers\ArrayHandler;
use App\Libraries\Larocket\CoreHandlers\DateHandler;
use Illuminate\Support\Facades\Schema;

class QueryBuilderParts
{
    protected $aliases = [
        'eq' => '=',
        'neq' => '<>',
        'gt' => '>',
        'gte' => '>=',
        'lt' => '<',
        'lte' => '<=',
        'nlike' => 'not like',
        'nin' => 'not in',
        'in' => 'in'
    ];

    protected $select_param = [
        'table' => [
            'id',
            'name' => [
                'as' => 'username',
            ],
            'created_at' => [
                'date' => 'Y-m-d h:i',
                'as' => 'created',
            ],
            [
                'count' => '',
                'as' => 'count',
            ],
            [
                'concat' => ['users.id', 'users.username'],
                'as' => 'concat',
            ],
        ],
    ];
    public function select($builder, $fields)
    {
        $selects = [];
        foreach ($fields as $index => $config) :
            $select = $this->selectField($index, $config);
            array_push($selects, $select);
        endforeach;
        foreach ($selects as $select) :
            $builder->selectRaw($select);
        endforeach;
        return $builder;
    }
    public function selects($builder, $table_fields)
    {
        $selects = [];
        foreach ($table_fields as $table => $fields) :
            foreach ($fields as $index => $config) :
                $select = $this->selectField($index, $config, $table);
                array_push($selects, $select);
            endforeach;
        endforeach;
        foreach ($selects as $select) :
            $builder->selectRaw($select);
        endforeach;
        return $builder;
    }
    public function selectField($index, $config, $table = "")
    {
        // print_r($index);
        // print_r($config);
        $select = $index;
        if ($table !== "") :
            $select = $table . '.' . $select;
        endif;

        if (is_string($config)) :
            if ($table !== "") :
                $config = "$table.$config";
            endif;
            $select = $config;

        elseif (is_array($config)) :
            if (is_int($index)) :
                if (isset($config['count'])) :
                    $format = $config['count'];
                    if (in_array($format, ['', '*', null, true, false])) :
                        $select = "COUNT(*)";
                    else :
                        $select = "COUNT($format)";
                    endif;
                elseif (isset($config['concat'])) :
                    $format = $config['concat'];
                    $format = implode(',', $format);
                    $select = "CONCAT($format)";
                endif;
            else :
                if ($table !== "") :
                    $index = "$table.$index";
                endif;
                if (isset($config['date'])) :
                    $format = $config['date'];
                    $format = DateHandler::percent($format);
                    $select = "DATE_FORMAT($index,'$format')";
                endif;
            endif;

            if (isset($config['as'])) :
                $format = $config['as'];
                $select .= " as $format";
            endif;
        endif;

        return $select;
    }

    public function filter($table, $fields)
    {
        $select = DB::getSchemaBuilder()->getColumnListing($table);
        $fields = ArrayHandler::filterValue($select, $fields);
        return $fields;
    }
    public function filters($tables, $fields)
    {
        $all_fields = [];
        foreach ($tables as $table) :
            $all_fields[$table] = $this->filter($table, $fields);
        endforeach;
        return $all_fields;
    }

    protected $where_param = [
        'field' => [
            'between' => [],
            'nbetween' => [],
            'in' => [],
            'nin' => [],
            'like' => [],
            'nlike' => [],
            'neq' => '',
            'gt' => '',
            'gte' => '',
            'lt' => '',
            'lte' => '',
            'null' => true,
            'date' => '',
            'month' => '',
            'day' => '',
            'year' => '',
            'time' => '',
        ],
        'field' => 'value',
        'and' => [],
        'or' => [],
    ];
    public function where($builder, $fields = [])
    {
        foreach ($fields as $field => $value) :
            if ($field == "or") :
                $builder->orWhere(function ($query) use ($value) {
                    $query = $this->where($query, $value);
                });
            elseif ($field == "and") :
                $builder->where(function ($query) use ($value) {
                    $query = $this->where($query, $value);
                });
            elseif (is_string($value) || is_int($value)) :
                $builder = $this->whereFields($builder, $field, 'eq', $value);
            elseif (is_array($value)) :
                if (ArrayHandler::isAsso($value)) :
                    foreach ($value as $condition => $val) :
                        $builder = $this->whereFields($builder, $field, $condition, $val);
                    endforeach;
                endif;
            endif;
        endforeach;
        return $builder;
    }
    public function whereFields($builder, $field, $condition, $val)
    {
        switch ($condition):
            case 'between':
                $builder->whereBetween($field, [$val[0], $val[1]]);
                break;
            case 'orbetween':
                $builder->orWhereBetween($field, [$val[0], $val[1]]);
                break;
            case 'nbetween':
                $builder->whereNotBetween($field, [$val[0], $val[1]]);
                break;
            case 'ornbetween':
                $builder->orWhereNotBetween($field, [$val[0], $val[1]]);
                break;
            case 'in':
                $builder->whereIn($field, (!is_array($val) ? [$val] : $val));
                break;
            case 'orin':
                $builder->orWhereIn($field, (!is_array($val) ? [$val] : $val));
                break;
            case 'nin':
                $builder->whereNotIn($field, (!is_array($val) ? [$val] : $val));
                break;
            case 'ornin':
                $builder->orWhereNotIn($field, (!is_array($val) ? [$val] : $val));
                break;
            case 'null':
                if ($val) :
                    $builder->whereNull($field);
                else :
                    $builder->whereNotNull($field);
                endif;
                break;
            case 'ornull':
                if ($val) :
                    $builder->orWhereNull($field);
                else :
                    $builder->orWhereNotNull($field);
                endif;
                break;
            case 'date':
                $builder->whereDate($field, $val);
                break;
            case 'ordate':
                $builder->orWhereDate($field, $val);
                break;
            case 'month':
                $builder->whereMonth($field, $val);
                break;
            case 'ormonth':
                $builder->orWhereMonth($field, $val);
                break;
            case 'day':
                $builder->whereDay($field, $val);
                break;
            case 'orday':
                $builder->orWhereDay($field, $val);
                break;
            case 'year':
                $builder->whereYear($field, $val);
                break;
            case 'oryear':
                $builder->orWhereYear($field, $val);
                break;
            case 'time':
                $builder->whereTime($field, $val);
                break;
            case 'ortime':
                $builder->orWhereTime($field, $val);
                break;
            case 'column':
                $builder->whereColumn($field, $val);
                break;
            case 'orcolumn':
                $builder->orWhereColumn($field, $val);
                break;
            case 'oreq':
                $builder->orWhere($field, $this->aliases['eq'], $val);
                break;
            case 'eq':
            default:
                $builder->where($field, $this->aliases[$condition], $val);
                break;
        endswitch;
        return $builder;
    }

    public function order($builder, $params)
    {
        foreach ($params as $column => $order) :
            if ($order == "asc") :
                $builder->orderBy($column);
            elseif ($order == "desc") :
                $builder->orderByDesc($column);
            endif;
        endforeach;
        return $builder;
    }

    public function limit($builder, $limit)
    {
        $builder->take($limit);
        return $builder;
    }

    public function offset($builder, $offset)
    {
        $builder->skip($offset);
        return $builder;
    }

    public function group($builder, $group)
    {
        $builder->groupBy($group);
        return $builder;
    }

    public function having($builder, $haves)
    {
        foreach ($haves as $field => $have) :
            if (is_string($have) || is_int($have)) :
                $builder = $builder->having($field, '=', $have);
            elseif (is_array($have)) :
                foreach ($have as $condition => $val) :
                    $builder->having($field, $this->aliases[$condition], $val);
                endforeach;
            endif;
        endforeach;
        return $builder;
    }

    protected $join_param = [
        'left' => [
            'user_profile' => ['users.id' => 'user_profile.user_id']
        ],
    ];
    public function join($builder, $params)
    {
        foreach ($params as $method => $joints) :
            foreach ($joints as $target => $joint) :
                $column1 = array_key_first($joint);
                $column2 = reset($joint);
                switch ($method):
                    case 'inner':
                        $builder->join($target, $column1, "=", $column2);
                        break;
                    case 'left':
                        $builder->leftJoin($target, $column1, "=", $column2);
                        break;
                    case 'right':
                        $builder->rightJoin($target, $column1, "=", $column2);
                        break;
                    case 'cross':
                        $builder->crossJoin($target, $column1, "=", $column2);
                        break;
                    default:
                        break;
                endswitch;
            endforeach;
        endforeach;
        return $builder;
    }

    public function set($builder, $data)
    {
        $builder->update($data);
        return $builder;
    }

    public function anywhere($builder, $anywhere, $table)
    {
        $columns = DB::getSchemaBuilder()->getColumnListing($table);
        $count = 0;
        foreach ($columns as $column) :
            if ($count == 0) :
                $builder->where($column, 'LIKE', "%$anywhere%");
            else :
                $builder->orWhere($column, 'LIKE', "%$anywhere%");
            endif;
            $count++;
        endforeach;
        return $builder;
    }
}
