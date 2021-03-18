<?php

namespace App\Libraries\Larocket\ArrayFormBuilder;

class ArrayFormBuilder extends GeneratedBuilder
{
    public $params = [
        'open' => [
            'action' => [
                'url' => '/ajax/update',
                'method' => 'put',
                // 'route' => '',
                // 'action' => '',
            ],
            // 'attribute' => [],
        ],
        'content' => [
            'email' => [
                'type' => 'email',
                'value' => 'example@gmail.com',
                'attribute' => [
                    'class' => 'form-control mb-2',
                    'placeholder' => 'enter email',
                ],
            ],
            'password' => [
                'type' => 'password',
            ],
            'image' => [
                'label' => 'cover',
                'type' => 'file',
            ],
            'applied' => [
                'type' => 'checkbox',
                'value' => 'yes',
                'checked' => true,
            ],
            'radio' => [
                'type' => 'radio',
                'vavlue' => 'yo',
                'checked' => true,
            ],
            'price' => [
                'type' => 'number',
                'value' => 20,
            ],
            'birthday' => [
                'type' => 'date',
                // 'value' => \Carbon\Carbon::now(),
            ],
            'size' => [
                'type' => 'select',
                'list' => ['L' => 'Large', 'S' => 'Small'],
                'value' => 'S',
                'attribute' => [
                    'placeholder' => 'pick one...'
                ]
            ],
            'amount' => [
                'type' => 'range',
                'min' => 10,
                'max' => 20,
                'value' => 15
            ],
            'day' => [
                'type' => 'range',
                'min' => 1,
                'max' => 31,
                'value' => 15
            ],
            'month' => [
                'type' => 'month',
                'value' => 4,
            ],
            'year' => [
                'type' => 'year',
                'min' => 1900,
                'max' => 2015,
                'value' => 2000,
            ],
        ],
        'config' => [
            'translate' => false,
        ]
    ];

    public function setup($params = [], $data = [])
    {
        if (!isset($params['open']['attribute']) || !count($params['open']['attribute'])) :
            $params['open']['attribute'] = ['class' => 'form-group'];
        endif;

        foreach ($params['content'] as $name => &$config) :

            if (!isset($config['value']) && !in_array($config['type'], ['number'])) :
                $config['value'] = '';
            elseif (in_array($config['type'], ['number'])) :
                $config['value'] = '';
            endif;

            if (!isset($config['label'])) :
                if (isset($params['config']['translate']) && $params['config']['translate']) :
                    $trans_name = 'columns.' . $name;
                    $config['label'] = $label =  __($trans_name);
                    if ($label == $trans_name) :
                        $config['label'] = $label = ucfirst($name);
                    endif;
                else :
                    $config['label'] = $label = ucfirst($name);
                endif;
            else:
                $label = ucfirst($config['label']);
            endif;

            if (in_array($config['type'], ['checkbox', 'radio'])) :
                if (!isset($config['checkbox']) || !isset($config['radio'])) :
                    $config['checked'] = false;
                endif;
            endif;

            if (!isset($config['attribute']['class'])) :
                if (in_array($config['type'], ['radio', 'checkbox', 'file'])) :
                    $config['attribute']['class'] = '';
                else :
                    $config['attribute']['class'] = 'form-control';
                endif;
            endif;

            if (!isset($config['attribute']['placeholder'])) :
                if (in_array($config['type'], ['radio', 'checkbox', 'file'])) :
                    $config['attribute']['placeholder'] = '';
                else :
                    $config['attribute']['placeholder'] = $label;
                endif;
            endif;

        endforeach;

        return $params;
    }
    public function read($params = [])
    {
    }
    public function create($params = [])
    {
    }
    public function update($params = [])
    {
    }
    public function delete($params = [])
    {
    }
}
