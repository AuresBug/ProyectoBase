<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'name' => 'required|string|max:255',                      // Limita la longitud del nombre
            'email' => 'required|email|unique:users,email|max:255',   // Asegura un formato de email válido y evita duplicados
            'password' => 'nullable|min:8|confirmed',                 // La contraseña es opcional, pero debe tener al menos 8 caracteres
            'avatar' => 'nullable|image|mimes:png,jpg,jpeg|max:2048', // Verifica que el archivo sea una imagen y limita el tamaño a 2MB
            'roles.*' => 'required|exists:roles,id',                  // Asegura que cada rol seleccionado existe en la base de datos
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
