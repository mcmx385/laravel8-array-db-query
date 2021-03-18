<?php

namespace App\Libraries\Larocket\ArrayFormBuilder;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Libraries\Larocket\CoreHandlers\ArrayHandler;

class GeneratedBuilder
{
    protected $assoSelect = [
        'user_profile' => [
            'user_id' => [
                'table' => 'users',
                'is' => 'id',
                'as' => ['name'],
            ]
        ]
    ];
    protected $customSelect = [
        'user_profile' => [
            'gender' => [
                'male' => 'M',
                'female' => 'F'
            ]
        ]
    ];
    protected $columnDefs = [
        'table' => '',
        'select' => [],
        'filter' => [],
        'required' => [],
    ];
    public function column(array $params)
    {
        $table = $params['table'];

        $configs = [];
        $filters = ['id', 'created_at', 'updated_at'];
        if (isset($params['filter'])) :
            $filters = array_merge($params['filter'], $filters);
        endif;
        $columns = DB::select("DESCRIBE $table");

        if (isset($params['select']) && count($params['select']) > 0) :
            $selects = $params['select'];
            $new_columns = [];
            foreach ($columns as $column) :
                if (in_array($column->Field, $selects)) :
                    array_push($new_columns, $column);
                endif;
            endforeach;
            $columns = $new_columns;

        elseif (count($filters) > 0) :
            $new_columns = [];
            foreach ($columns as $column) :
                if (!in_array($column->Field, $filters)) :
                    array_push($new_columns, $column);
                endif;
            endforeach;
            $columns = $new_columns;

        endif;

        foreach ($columns as $column) :
            $field = $column->Field;

            $type = $column->Type;
            $input_type = $this->type($type, $field);
            $configs[$field]['type'] = $input_type;

            if (isset($params['required']) && in_array($field, $params['required'])) :
                $configs[$field]['attribute']['required'] = true;
            endif;

            if (isset($this->assoSelect[$table][$field])) :

                $assoTable = $this->assoSelect[$table][$field]['table'];
                $assoField = $this->assoSelect[$table][$field]['is'];
                $assoDisplay = $this->assoSelect[$table][$field]['as'][0];

                $group = DB::table($assoTable)->select($assoDisplay)->groupBy($assoField)->get();
                $group = ArrayHandler::onlyCol($group, $assoDisplay);

                $configs[$field]['type'] = 'select';
                $configs[$field]['list'] = $group;
                $configs[$field]['label'] = $assoDisplay;
            
            elseif (isset($this->customSelect[$table][$field])) :
                $configs[$field]['type'] = 'select';
                $configs[$field]['list'] = $this->customSelect[$table][$field];
            endif;

        endforeach;

        return $configs;
    }
    public function type($type, $field)
    {
        $input_type = 'text';

        // By name
        if (strpos($field, "mail") !== false) :
            $input_type = "email";
        elseif (strpos($field, "image") !== false || in_array($field, ['avatar', 'cover_image'])) :
            $input_type = "file";
        elseif (strpos($field, "range") !== false) :
            $input_type = "range";
        elseif (strpos($field, "month") !== false) :
            $input_type = "month";
        elseif (strpos($field, "year") !== false) :
            $input_type = "year";
        elseif (strpos($field, "password") !== false) :
            $input_type = "password";

        // By type
        elseif ($type == "date") :
            $input_type = 'date';
        elseif ($type == "datetime") :
            $input_type = 'datetime';
        elseif (strpos($type, "char") !== false) :
            $input_type = "text";
        elseif (strpos($type, "int") !== false) :
            $input_type = "number";
        elseif (in_array($type, ['smalltext', 'mediumtext', 'longtext'])) :
            $input_type = "textarea";
        endif;

        return $input_type;
    }
    public function display($table, array $filter = [])
    {
        $def_filter = ['id', 'created_at', 'updated_at'];
        $filter = array_merge($filter, $def_filter);
        $columns = DB::select("DESCRIBE $table");
        $params = [];
        foreach ($columns as $column) :
            $field = $column->Field;
            if (!in_array($field, $filter)) :
                $params[$field]['type'] = 'display';
            endif;
        endforeach;
        return $params;
    }
    public function value($form_content, $data)
    {
        foreach ($form_content as $column => &$config) :
            if (isset($data->$column)) :
                if (empty($data->$column)) :
                    $config['value'] = 'null';
                else :
                    $config['value'] = $data->$column;
                endif;
            else :
                unset($form_content[$column]);
            endif;
        endforeach;
        return $form_content;
    }
}
