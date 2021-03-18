<?php

namespace App\Libraries\Larocket\ArrayQueryBuilder;

use Illuminate\Support\Facades\DB;
use App\Libraries\Larocket\CoreHandlers\ArrayHandler;
use App\Libraries\Larocket\ArrayQueryBuilder\QueryBuilderParts;

class ArrayQueryBuilder extends QueryBuilderParts
{
    public function query($params = [])
    {
        $results = [];
        foreach ($params as $action => $param) :
            $result = $this->$action($param);
            array_push($results, $result);
        endforeach;
        return $results;
    }

    protected $create_param = [
        'table' => '',
        'set' => [],
        'dup' => false,
        'if' => [],
    ];
    public function create($param = [])
    {
        $table = $param['table'];
        $builder = DB::table($table);
        if (ArrayHandler::isAsso($param)) :
            $builder->insertGetId($param['set']);
        else :
            if (isset($param['dup']) && $param['dup']) :
                $builder->insertOrIgnore($param['set']);
            else :
                $builder->insert($param['set']);
            endif;
        endif;
    }

    protected $read_param = [
        'table' => 'users',
        'select' => [
            'users' => [
                'id',
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
            'user_profile' => ['gender']
        ],
        'filter' => ['field'],
        'where' => [],
        'anywhere' => '',
        'order' => [
            'field' => 'asc',
            'field' => 'desc',
        ],
        'limit' => 10,
        'offset' => 0,
        'group' => 'field',
        'having' => [
            'field' => ''
        ],
        'join' => [
            'left' => [
                'user_profile' => ['users.id' => 'user_profile.user_id']
            ]
        ],
        'if' => [
            'table' => 'table',
            'where' => [],
        ],
    ];
    public function read($param = [])
    {
        $table = $param['table'];
        $builder = DB::table($table);

        if (!isset($param['join'])) :
            if (isset($param['filter'])) :
                $param['select'] = $this->filter($table, $param['filter']);
            endif;
        else :
            if (isset($param['filter'])) :
                $tables = [$table];
                foreach ($param['join'] as $method => $joint) :
                    foreach ($joint as $target => $columns) :
                        array_push($tables, $target);
                    endforeach;
                endforeach;
                $param['select'] = $this->filters($tables, $param['filter']);
            endif;

            if (isset($param['join'])) :
                $builder = $this->join($builder, $param['join']);
            endif;
        endif;

        if (isset($param['select'])) :
            if (isset($param['join'])) :
                $builder = $this->selects($builder, $param['select']);
            else :
                $builder = $this->select($builder, $param['select']);
            endif;
        endif;

        if (isset($param['where'])) :
            $builder = $this->where($builder, $param['where']);
        endif;

        if (isset($param['anywhere'])) :
            $builder = $this->anywhere($builder, $param['anywhere'], $table);
        endif;

        if (isset($param['order'])) :
            $builder = $this->order($builder, $param['order']);
        endif;

        if (isset($param['limit'])) :
            $builder = $this->limit($builder, $param['limit']);
        endif;

        if (isset($param['offset'])) :
            $builder = $this->offset($builder, $param['offset']);
        endif;

        if (isset($param['group'])) :
            $builder = $this->group($builder, $param['group']);
        endif;

        if (isset($param['having'])) :
            $builder = $this->having($builder, $param['having']);
        endif;

        $result = $builder->get();
        if (isset($param['select']) && count($param['select']) == 1) :
            print_r($param['select']);
            if (is_string($param['select'])) :
                $result = ArrayHandler::onlyCol($result, $param['select'][0]);
            elseif (is_array($param['select']) && count(reset($param['select'])) == 1 && is_string(reset($param['select'])[0])) :
                $result = ArrayHandler::onlyCol($result, reset($param['select'])[0]);
            endif;
        endif;
        // $builder->dd();
        return $result;
    }

    protected $update_param = [
        'table' => '',
        'set' => [],
        'where' => [],
        'inc' => [],
        'dec' => [],
        'if' => [],
    ];
    public function update($param = [])
    {
        $table = $param['table'];
        $builder = DB::table($table);

        if (isset($param['where'])) :
            $builder = $this->where($builder, $param['where']);

            if (isset($param['inc'])) :
                foreach ($param['inc'] as $column => $amount) :
                    $builder->increment($column, $amount, $param['where']);
                endforeach;
            endif;

            if (isset($param['dec'])) :
                foreach ($param['dec'] as $column => $amount) :
                    $builder->decrement($column, $amount, $param['where']);
                endforeach;
            endif;
        endif;

        if (isset($param['set'])) :
            $builder = $this->set($builder, $param['set']);
        endif;
    }

    protected $exist_param = [
        'table' => '',
        'set' => [],
        'where' => [],
        'refer' => [],
        'alter' => [],
    ];
    public function exist($param = [])
    {
        $table = $param['table'];
        $builder = DB::table($table);

        if (isset($param['set']) && isset($param['where'])) :
            $builder->updateOrInsert($param['where'], $param['set']);
        elseif (isset($param['set']) && isset($param['refer']) && isset($param['alter'])) :
            $builder->upsert($param['set'], $param['refer'], $param['alter']);
        endif;
    }

    protected $delete_param = [
        'table' => '',
        'where' => [],
        'trunc' => true,
        'if' => [],
    ];
    public function delete($param = [])
    {
        $table = $param['table'];
        $builder = DB::table($table);

        if (isset($param['where'])) :
            $builder = $this->where($builder, $param['where']);

            $builder->delete();
        endif;

        if (isset($param['trunc']) && $param['trunc']) :
            $builder->truncate();
        endif;
    }
}
