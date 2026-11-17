<?php

return [
    'accepted' => 'O campo :attribute deve ser aceito.',
    'alpha' => 'O campo :attribute deve conter apenas letras.',
    'confirmed' => 'A confirmação do campo :attribute não corresponde.',
    'email' => 'O campo :attribute deve conter um endereço de e-mail válido.',
    'exists' => 'O valor selecionado para :attribute é inválido.',
    'in' => 'O valor selecionado para :attribute é inválido.',
    'integer' => 'O campo :attribute deve ser um número inteiro.',
    'max' => [
        'string' => 'O campo :attribute não pode ter mais de :max caracteres.',
        'numeric' => 'O campo :attribute não pode ser maior que :max.',
    ],
    'min' => [
        'string' => 'O campo :attribute deve ter pelo menos :min caracteres.',
        'numeric' => 'O campo :attribute deve ser pelo menos :min.',
    ],
    'nullable' => 'O campo :attribute pode ficar vazio.',
    'required' => 'O campo :attribute é obrigatório.',
    'required_if' => 'O campo :attribute é obrigatório quando :other é :value.',
    'string' => 'O campo :attribute deve ser um texto.',
    'unique' => 'O valor informado para :attribute já está em uso.',

    'attributes' => [
        'name' => 'nome',
        'email' => 'e-mail',
        'password' => 'senha',
        'role' => 'perfil',
        'congregacao_id' => 'congregação',
        'nome' => 'nome',
        'descricao' => 'descrição',
        'data_inicio' => 'data de início',
        'data_fim' => 'data de término',
        'situacao' => 'situação',
    ],
];