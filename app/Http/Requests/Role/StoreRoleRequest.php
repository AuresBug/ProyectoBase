<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Cambiar si deseas implementar autorización específica
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:roles,name', // Limita la longitud del nombre y lo hace único
            'permissions.*' => 'required|exists:permissions,id',   // Verifica que cada permiso exista en la tabla de permisos
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
            'name'          => 'nombre',
            'permissions.*' => 'permisos',
        ];
    }
}
