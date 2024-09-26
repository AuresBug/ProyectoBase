<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',                                       // Limita la longitud del nombre
            'email' => 'required|email|max:255|unique:users,email,' . $this->user->id, // Verifica la unicidad del email, exceptuando el del usuario actual
            'password' => 'nullable|min:8|confirmed',                                  // La contraseña es opcional, pero debe tener al menos 8 caracteres y confirmarse
            'avatar' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',                  // Verifica que sea una imagen y limita el tamaño a 2MB
            'roles.*' => 'required|exists:roles,id',                                   // Asegura que los roles existen en la tabla 'roles'
        ];
    }

/**
 * Get custom attribute names for validation.
 *
 * @return array
 */
    public function attributes()
    {
        return [
            'name'     => 'nombre',
            'email'    => 'correo electrónico',
            'password' => 'contraseña',
            'avatar'   => 'avatar',
            'roles.*'  => 'roles',
        ];
    }

}
