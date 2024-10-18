<?php
require_once __DIR__ . '/../../../models/loginModels/loginClienteModel.php';


class LoginController {
    private $model;

    public function __construct(){
        $this->model = new LoginModel();
    }

    public function login($usuario, $password){
        return $this->model->login($usuario, $password);
    }
}
?>
